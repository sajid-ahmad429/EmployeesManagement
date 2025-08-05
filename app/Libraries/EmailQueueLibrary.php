<?php

namespace App\Libraries;

use CodeIgniter\I18n\Time;
use Config\Email as EmailConfig;
use Config\Services;

/**
 * Email Queue Library for Asynchronous Email Processing
 * Reduces loading times by queuing emails instead of sending immediately
 */
class EmailQueueLibrary
{
    private $emailConfig;
    private $db;
    private $cache;

    public function __construct()
    {
        $this->emailConfig = new EmailConfig();
        $this->db = \Config\Database::connect();
        $this->cache = Services::cache();
    }

    /**
     * Queue an email for asynchronous sending
     * 
     * @param array $emailData
     * @return bool
     */
    public function queueEmail($emailData)
    {
        $data = [
            'to_email' => $emailData['to'] ?? '',
            'to_name' => $emailData['to_name'] ?? '',
            'subject' => $emailData['subject'] ?? '',
            'message' => $emailData['message'] ?? '',
            'template' => $emailData['template'] ?? '',
            'priority' => $emailData['priority'] ?? 3,
            'status' => 'pending',
            'attempts' => 0,
            'max_attempts' => 3,
            'created_at' => new Time('now'),
            'scheduled_at' => $emailData['scheduled_at'] ?? new Time('now'),
        ];

        try {
            $this->db->table('email_queue')->insert($data);
            
            // Trigger background processing if available
            $this->triggerBackgroundProcessing();
            
            return true;
        } catch (\Exception $e) {
            log_message('error', 'Failed to queue email: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Process queued emails in batches
     * 
     * @param int $batchSize
     * @return int Number of emails processed
     */
    public function processQueue($batchSize = 10)
    {
        $processed = 0;
        
        // Get pending emails
        $emails = $this->db->table('email_queue')
            ->where('status', 'pending')
            ->where('scheduled_at <=', new Time('now'))
            ->where('attempts <', 'max_attempts')
            ->limit($batchSize)
            ->get()
            ->getResultArray();

        foreach ($emails as $email) {
            if ($this->sendQueuedEmail($email)) {
                $processed++;
            }
        }

        return $processed;
    }

    /**
     * Send a single queued email
     * 
     * @param array $email
     * @return bool
     */
    private function sendQueuedEmail($email)
    {
        try {
            $emailService = Services::email();
            
            // Configure email service
            $emailService->setFrom($this->emailConfig->fromEmail, $this->emailConfig->fromName);
            $emailService->setTo($email['to_email']);
            $emailService->setSubject($email['subject']);
            $emailService->setMessage($email['message']);
            
            // Send email
            if ($emailService->send()) {
                // Mark as sent
                $this->db->table('email_queue')
                    ->where('id', $email['id'])
                    ->update([
                        'status' => 'sent',
                        'sent_at' => new Time('now'),
                        'updated_at' => new Time('now')
                    ]);
                return true;
            } else {
                throw new \Exception('Email service failed to send');
            }
        } catch (\Exception $e) {
            // Increment attempts
            $this->db->table('email_queue')
                ->where('id', $email['id'])
                ->update([
                    'attempts' => $email['attempts'] + 1,
                    'status' => ($email['attempts'] + 1 >= $email['max_attempts']) ? 'failed' : 'pending',
                    'error_message' => $e->getMessage(),
                    'updated_at' => new Time('now')
                ]);
            
            log_message('error', 'Failed to send queued email: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Trigger background processing
     */
    private function triggerBackgroundProcessing()
    {
        // Use cache to prevent multiple triggers
        $cacheKey = 'email_queue_processing';
        if ($this->cache->get($cacheKey)) {
            return;
        }

        // Set cache for 30 seconds to prevent overlapping processes
        $this->cache->save($cacheKey, true, 30);

        // In production, you might want to use a proper job queue system
        // For now, we'll use a simple approach
        if (function_exists('fastcgi_finish_request')) {
            fastcgi_finish_request();
        }
    }

    /**
     * Get queue statistics
     * 
     * @return array
     */
    public function getQueueStats()
    {
        $stats = $this->db->table('email_queue')
            ->select('status, COUNT(*) as count')
            ->groupBy('status')
            ->get()
            ->getResultArray();

        $result = [
            'pending' => 0,
            'sent' => 0,
            'failed' => 0,
            'total' => 0
        ];

        foreach ($stats as $stat) {
            $result[$stat['status']] = (int)$stat['count'];
            $result['total'] += (int)$stat['count'];
        }

        return $result;
    }

    /**
     * Clean old processed emails
     * 
     * @param int $daysOld
     * @return int Number of records deleted
     */
    public function cleanOldEmails($daysOld = 30)
    {
        $cutoffDate = new Time("-{$daysOld} days");
        
        return $this->db->table('email_queue')
            ->where('status IN', ['sent', 'failed'])
            ->where('created_at <', $cutoffDate)
            ->delete();
    }
}