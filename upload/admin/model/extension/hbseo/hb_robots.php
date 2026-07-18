<?php
class ModelExtensionHbseoHbRobots extends Model {
	public function install(){
		$this->createDefaultRobotsFile();
		$this->create_backup();
	}
	
	public function uninstall() {
		return true;
	}

	public function create_backup() {
		$robots_file = $this->web_root . '/robots.txt'; // Path to robots.txt
		$backup_file = $this->web_root . '/robots.txt.backup'; // Path to backup file
	
		if (file_exists($robots_file)) {
			try {
				copy($robots_file, $backup_file); // Create a backup
				return true; // Backup successful
			} catch (Exception $e) {
				return false; // Backup failed
			}
		}
	
		return false; // robots.txt does not exist
	}

	public function restore_backup() {
		$robots_file = $this->web_root . '/robots.txt'; // Path to robots.txt
		$backup_file = $this->web_root . '/robots.txt.backup'; // Path to backup file
	
		if (file_exists($backup_file)) {
			try {
				copy($backup_file, $robots_file); // Restore backup
				return true; // Restore successful
			} catch (Exception $e) {
				return false; // Restore failed
			}
		}
	
		return false; // Backup file does not exist
	}
	
	public function createDefaultRobotsFile() {
		$file_path = $this->web_root . '/robots.txt'; // Path to the robots.txt file relative to the root
		$sitemap_path = rtrim(HTTPS_CATALOG, '/') . '/sitemap-index.xml';
	
		// Check if the file exists
		if (!file_exists($file_path)) {
			// Default robots.txt content
			$default_content = <<<EOT
User-agent: facebookexternalhit
User-agent: Facebot
User-agent: meta-externalagent
User-agent: meta-externalfetcher
Allow: /

User-agent: *

Disallow: /*?page=$
Disallow: /*&page=$
Disallow: /*?sort=
Disallow: /*&sort=
Disallow: /*?order=
Disallow: /*&order=
Disallow: /*?limit=
Disallow: /*&limit=
Disallow: /*?filter_name=
Disallow: /*&filter_name=
Disallow: /*?filter_sub_category=
Disallow: /*&filter_sub_category=
Disallow: /*?filter_description=
Disallow: /*&filter_description=
Sitemap: {$sitemap_path}
EOT;
	
			// Attempt to create the file
			try {
				file_put_contents($file_path, $default_content); // Write default content to robots.txt
			} catch (Exception $e) {
				// Handle error in case of failure (e.g., permissions issue)
				throw new Exception('Error creating robots.txt: ' . $e->getMessage());
			}
		}
	}
	


}
?>
