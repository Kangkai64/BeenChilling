<?php

class SimplePager {
    public $limit;      // Page size
    public $page;       // Current page
    public $item_count; // Total item count
    public $page_count; // Total page count
    public $result;     // Result set (array of records)
    public $count;      // Item count on the current page

    public function __construct($query, $params, $limit, $page) {
        global $_db;

        // Set [limit] and [page]
        $this->limit = ctype_digit("$limit") ? max($limit, 1) : 10;
        $this->page = ctype_digit("$page") ? max($page, 1) : 1;

        // For queries with GROUP BY, we need a different approach
        if (stripos($query, 'GROUP BY') !== false) {
            // Extract the base query up to the GROUP BY part
            $base_query = $query;
            
            // Create a subquery for counting
            $count_query = "SELECT COUNT(*) FROM ($base_query) as count_table";
            
            // Set [item count]
            $stm = $_db->prepare($count_query);
            $stm->execute($params);
            $this->item_count = $stm->fetchColumn();
        } else {
            // Original approach for simple queries
            $q = preg_replace('/SELECT\s+.+?\s+FROM/is', 'SELECT COUNT(*) FROM', $query, 1);
            $stm = $_db->prepare($q);
            $stm->execute($params);
            $this->item_count = $stm->fetchColumn();
        }

        // Set [page count]
        $this->page_count = ceil($this->item_count / $this->limit);
        
        // Adjust page if out of bounds
        if ($this->page > $this->page_count && $this->page_count > 0) {
            $this->page = $this->page_count;
        }

        // Calculate offset
        $offset = ($this->page - 1) * $this->limit;

        // Set [result]
        $stm = $_db->prepare($query . " LIMIT $offset, $this->limit");
        $stm->execute($params);
        $this->result = $stm->fetchAll();

        // Set [count]
        $this->count = count($this->result);
    }

    public function html($href = '', $attr = '') {
        if (!$this->result) return;

        // Generate pager (html)
        $prev = max($this->page - 1, 1);
        $next = min($this->page + 1, $this->page_count);

        echo "<nav class='pager' $attr>";
        if ($this->page > 1) {
            echo "<a href='?page=$prev&$href'>Previous</a>";
        }

        for ($p = 1; $p <= $this->page_count; $p++) {
            $c = $p == $this->page ? 'active' : '';
            echo "<a href='?page=$p&$href' class='$c'>$p</a>";
        }

        if ($this->page < $this->page_count) {
            echo "<a href='?page=$next&$href'>Next</a>";
        }

        echo "</nav>";
    }
}