<?php
/**
 * Fix broken views by ensuring all functions exist first
 */

require_once __DIR__ . '/includes/security.php';

try {
    $pdo = db();
    
    // First, check which functions exist
    $stmt = $pdo->query("SELECT ROUTINE_NAME FROM INFORMATION_SCHEMA.ROUTINES WHERE ROUTINE_SCHEMA = DATABASE() AND ROUTINE_TYPE = 'FUNCTION'");
    $existingFunctions = $stmt->fetchAll(PDO::FETCH_COLUMN);
    echo "Existing functions: " . implode(', ', $existingFunctions ?: ['none']) . "\n\n";
    
    // Read and execute functions.sql
    $sql = file_get_contents(__DIR__ . '/database/functions.sql');
    if (!$sql) {
        echo "Error: Could not read functions.sql\n";
        exit(1);
    }
    
    // Split by DELIMITER statements and execute
    $parts = preg_split('/DELIMITER\s+(\S+)/i', $sql, -1, PREG_SPLIT_DELIM_CAPTURE);
    
    $inDelimiter = false;
    $delimiter = ';';
    $currentStatement = '';
    
    foreach ($parts as $i => $part) {
        $part = trim($part);
        if ($part === '' || $part === '$$' || strtolower($part) === 'delimiter') {
            continue;
        }
        
        // Check if this is a delimiter marker
        if (preg_match('/^\$\$$/', $part)) {
            $delimiter = '$$';
            continue;
        }
        
        // Execute statements split by current delimiter
        $statements = array_map('trim', explode($delimiter, $part));
        foreach ($statements as $stmt) {
            $stmt = trim($stmt);
            if ($stmt === '' || preg_match('/^--/', $stmt) || preg_match('/^USE\s/', $stmt)) {
                continue;
            }
            
            try {
                $pdo->exec($stmt);
                echo "✓ Executed: " . substr($stmt, 0, 60) . "...\n";
            } catch (PDOException $e) {
                if (strpos($e->getMessage(), 'already exists') !== false || 
                    strpos($e->getMessage(), 'Duplicate') !== false) {
                    echo "- Skipped (already exists): " . substr($stmt, 0, 40) . "...\n";
                } else {
                    echo "✗ Error on: " . substr($stmt, 0, 60) . "...\n";
                    echo "  " . $e->getMessage() . "\n";
                }
            }
        }
    }
    
    // Now recreate the views
    echo "\n--- Recreating Views ---\n";
    
    $viewsSql = file_get_contents(__DIR__ . '/database/views.sql');
    if (!$viewsSql) {
        echo "Error: Could not read views.sql\n";
        exit(1);
    }
    
    // Remove USE statement and split by semicolons
    $viewsSql = preg_replace('/USE\s+\S+;?/', '', $viewsSql);
    $statements = array_map('trim', explode(';', $viewsSql));
    
    foreach ($statements as $stmt) {
        $stmt = trim($stmt);
        if ($stmt === '' || preg_match('/^--/', $stmt)) {
            continue;
        }
        
        try {
            $pdo->exec($stmt);
            echo "✓ Executed: " . substr($stmt, 0, 60) . "...\n";
        } catch (PDOException $e) {
            echo "✗ Error: " . $e->getMessage() . "\n";
            echo "  Statement: " . substr($stmt, 0, 80) . "...\n\n";
        }
    }
    
    echo "\n=== Done ===\n";
    echo "Try accessing the reservations page again.\n";
    
} catch (Exception $e) {
    echo "Fatal error: " . $e->getMessage() . "\n";
}
