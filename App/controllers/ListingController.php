<?php

namespace App\Controllers;
namespace App\controllers;

use Framework\Database;
use Framework\Validation;

class ListingController {
    protected $db;
    public function __construct()
    {
        $config = require basePath('config/db.php');
        $this->db = new Database($config);
    }
    /**
     * show the latest listings
     * 
     * @return void
     */
    public function index() {
        $listings = $this->db->query('SELECT * from listings')->fetchAll();
 
        loadView('listings/index', [
        'listings' => $listings
    ]);
    }
    /**
     * show the create page 
     * 
     * @return void 
     */
    public function create() {
        loadView('listings/create');
    }
    /**
     * show single lsiting
     * 
     * @return void
     */
    public function show($params) {
        $id = $params['id'] ?? '';

        $params = [
        'id' => $id
    ];


        $listing = $this->db->query('SELECT * FROM listings WHERE id = :id', $params)->fetch();

        // Check if listing exists
        if (!$listing) {
            ErrorController::notFound('Listing not found');
            return;
          }

        loadView('listings/show', [
        'listing' => $listing
    ]);
    }

    /**
     * Store data in database 
     * 
     * @return void 
     */

     public function store() {
        $allowedFields = [
            'title', 'description', 'salary', 'tags',
            'company', 'address', 'city', 'state', 'phone', 'email', 'requirements', 'beneftis'   
    ];
        $newListingData = array_intersect_key($_POST, array_flip($allowedFields));

        $newListingData['user_id'] = 1;

        $newListingData = array_map('sanitize', $newListingData);

        $requiredFields = ['title', 'description', 'salary', 'email', 'city', 'state'];

        $errors = [];

        foreach($requiredFields as $field) {
            if (empty($newListingData[$field]) || !Validation::string($newListingData[$field])) {
                $errors[$field] =  ucfirst($field) . ' is required';
            }
        }

        if(!empty($errors)) {
            // Reload the view
            loadView('listings/create', [
                'errors' => $errors,
                'listing' => $newListingData 
            ]);
        } else {
            // Submit Data 
            $fields = [];

            foreach($newListingData as $field => $value) {
                $fields[] = $field;
            }

            $fields = implode(', ', $fields);

            $values = [];

            foreach($newListingData as $field => $value) {
                // COnvert empty strings to null 
                if($value === '') {
                    $newListingData[$field] = null;
                }

                $values[] = ':' . $field;
            }
            $values = implode(', ', $values);

            $query = "INSERT INTO listings ({$fields}) VALUES ({$values})";
            $this->db->query($query, $newListingData);

            redirect('/listings');
        }
     }
}