<?php
        class Sqlite {

			private $db;
			private $dbname;

			public function __construct($dbname)
			{
				$this->dbname = $dbname;
			}

			public function pdo()
			{
				return $this->db;
			}

			public function connect()
			{
				try {
					$this->db = new PDO('sqlite:' . $this->dbname);
					$this->db->setAttribute( PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION );
					return $this->db;
				}
				catch (PDOException $e) {
					echo $e->getMessage();
				}
			}

			public function createTable($tables)
			{
				try {
					foreach ($tables as $k => $table) {
						$command = "CREATE TABLE IF NOT EXISTS " . $table['name'] . "(" . implode(',', $table['fields']) . ")";
						$this->db->exec($command);
					}
				}
				catch (PDOException $e) {
					echo $e->getMessage();
				}
			}

			public function getTableList()
			{
				try {
					$q = $this->db->query("SELECT name FROM sqlite_master WHERE type='table' ORDER BY name");
					$r = $q->fetchAll();
					$q->closeCursor();
					return $r;
				}
				catch (PDOException $e) {
					echo $e->getMessage();
				}
			}

			public function insertLine($tablename, $line)
			{
				# parsing contents
				$qp1 = [];
				$qp2 = [];
				foreach ($line as $field => $value) {
					$qp1[] = $field;
					$qp2[] = ':' . $field;
				}
				# building query
				$command = "INSERT INTO $tablename( ". implode(',', $qp1) ." ) VALUES(". implode(',', $qp2) .")";
				# execution
				$q = $this->db->prepare($command);
				$r = $q->execute($line);
				$q->closeCursor();
				return $r ? $this->db->lastInsertId() : false;
			}

			public function updateLine($tablename, $line, $whereclause)
			{
				# parsing contents
				$qp1 = [];
				foreach ($line as $field => $value) {
					$qp1[] = $field. '=:' .$field;
				}
				# building query
				$command = "UPDATE $tablename SET $qp1 WHERE ". key($whereclause) ."=:". key($whereclause);
				# execution
				$q = $this->db->prepare($command);
				$r = $q->execute(array_merge($line, $whereclause));
				$q->closeCursor();
				return $r;
			}

			public function deleteLine($tablename, $whereclause)
			{
				# building query
				$command = "DELETE FROM $tablename WHERE ". key($whereclause) ."=:". key($whereclause);
				# execution
				$q = $this->db->prepare($command);
				$r = $q->execute($whereclause);
				$q->closeCursor();
				return $r;
			}
		}
