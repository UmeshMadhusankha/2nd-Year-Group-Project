<?php
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../models/RepairerModel.php';
require_once __DIR__ . '/../models/CompanyModel.php';

class ProviderController {
    private $repairerModel;
    private $companyModel;
    
    public function __construct() {
        global $pdo;
        $this->repairerModel = new Repairer($pdo);
        $this->companyModel = new Company($pdo);
    }
    
    /**
     * Get providers (both repairers and companies) for landing page
     * Returns JSON response
     */
    public function getProviders() {
        // Ensure no prior output (BOM, debug, accidental echoes) breaks JSON
        while (ob_get_level() > 0) {
            ob_end_clean();
        }
        header('Content-Type: application/json');
        
        try {
            // Get query parameters
            $limit = isset($_GET['limit']) ? (int)$_GET['limit'] : 12;
            $offset = isset($_GET['offset']) ? (int)$_GET['offset'] : 0;
            $categoryId = isset($_GET['category']) ? $_GET['category'] : null;
            $minRating = isset($_GET['rating']) ? (float)$_GET['rating'] : null;
            $location = isset($_GET['location']) ? $_GET['location'] : null;
            $providerType = isset($_GET['provider_type']) ? $_GET['provider_type'] : 'all';
            
            // Build filters
            $filters = [];
            if ($categoryId) {
                $filters['category_id'] = $categoryId;
            }
            if ($minRating) {
                $filters['min_rating'] = $minRating;
            }
            if ($location) {
                $filters['service_area'] = $location;
                $filters['location'] = $location;
            }
            
            $providers = [];
            
            // Fetch repairers if needed
            if ($providerType === 'all' || $providerType === 'individual') {
                $repairers = $this->repairerModel->getAll($filters, $limit, $offset);
                error_log("Fetched " . count($repairers) . " repairers");
                $providers = array_merge($providers, $repairers);
            }
            
            // Fetch companies if needed
            if ($providerType === 'all' || $providerType === 'company') {
                $companies = $this->companyModel->getAll($filters, $limit, $offset);
                error_log("Fetched " . count($companies) . " companies");
                error_log("Provider type filter: " . $providerType);
                $providers = array_merge($providers, $companies);
            }
            
            // Sort combined results by rating
            usort($providers, function($a, $b) {
                return $b['ratings'] <=> $a['ratings'];
            });
            
            // Limit to requested amount
            $providers = array_slice($providers, 0, $limit);
            
            // Get total counts
            $totalRepairers = $this->repairerModel->getCount($filters);
            $totalCompanies = $this->companyModel->getCount($filters);
            $totalProviders = $totalRepairers + $totalCompanies;
            
            echo json_encode([
                'success' => true,
                'data' => $providers,
                'pagination' => [
                    'total' => $totalProviders,
                    'limit' => $limit,
                    'offset' => $offset,
                    'hasMore' => ($offset + $limit) < $totalProviders
                ]
            ]);
            
        } catch (Exception $e) {
            error_log("Error in getProviders: " . $e->getMessage());
            http_response_code(500);
            echo json_encode([
                'success' => false,
                'message' => 'Failed to fetch providers',
                'error' => $e->getMessage()
            ]);
        }
        exit;
    }
    
    /**
     * Get featured providers for landing page
     */
    public function getFeatured() {
        // Ensure no prior output (BOM, debug, accidental echoes) breaks JSON
        while (ob_get_level() > 0) {
            ob_end_clean();
        }
        header('Content-Type: application/json');
        
        try {
            $limit = isset($_GET['limit']) ? (int)$_GET['limit'] : 12;
            $providerType = isset($_GET['provider_type']) ? $_GET['provider_type'] : 'all';
            
            $repairers = [];
            $companies = [];

            // Get top-rated repairers
            if ($providerType === 'all' || $providerType === 'individual') {
                $repairers = $this->repairerModel->getFeatured($limit, 0);
            }
            
            // Get top-rated companies
            if ($providerType === 'all' || $providerType === 'company') {
                $companies = $this->companyModel->getFeatured($limit, 0);
            }
            
            // Combine and sort by rating
            $providers = array_merge($repairers, $companies);
            usort($providers, function($a, $b) {
                return $b['ratings'] <=> $a['ratings'];
            });
            
            // Limit to requested amount
            $providers = array_slice($providers, 0, $limit);
            
            echo json_encode([
                'success' => true,
                'data' => $providers
            ]);
            
        } catch (Exception $e) {
            error_log("Error in getFeatured: " . $e->getMessage());
            http_response_code(500);
            echo json_encode([
                'success' => false,
                'message' => 'Failed to fetch featured providers'
            ]);
        }
        exit;
    }
    
    /**
     * Get single provider details
     */
    public function getProviderDetails() {
        // Ensure no prior output (BOM, debug, accidental echoes) breaks JSON
        while (ob_get_level() > 0) {
            ob_end_clean();
        }
        header('Content-Type: application/json');
        
        try {
            $providerId = isset($_GET['id']) ? (int)$_GET['id'] : 0;
            $providerType = isset($_GET['type']) ? $_GET['type'] : 'individual';
            
            if (!$providerId) {
                http_response_code(400);
                echo json_encode([
                    'success' => false,
                    'message' => 'Provider ID is required'
                ]);
                exit;
            }
            
            if ($providerType === 'individual') {
                $provider = $this->repairerModel->getById($providerId);
            } else {
                $provider = $this->companyModel->getById($providerId);
            }
            
            if ($provider) {
                echo json_encode([
                    'success' => true,
                    'data' => $provider
                ]);
            } else {
                http_response_code(404);
                echo json_encode([
                    'success' => false,
                    'message' => 'Provider not found'
                ]);
            }
            
        } catch (Exception $e) {
            error_log("Error in getProviderDetails: " . $e->getMessage());
            http_response_code(500);
            echo json_encode([
                'success' => false,
                'message' => 'Failed to fetch provider details'
            ]);
        }
        exit;
    }
}