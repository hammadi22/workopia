<?php

namespace App\Controllers;

use Framework\Database;

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
    public function show() {
        $id = $_GET['id'] ?? '';

        $params = [
        'id' => $id
    ];


        $listing = $this->db->query('SELECT * FROM listings WHERE id = :id', $params)->fetch();

        loadView('listings/show', [
        'listing' => $listing
    ]);
    }
}