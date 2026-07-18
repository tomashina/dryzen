<?php
class ModelExtensionHbseoHbSitemap extends Model {
	public function install(){
		$this->db->query("CREATE TABLE IF NOT EXISTS `" . DB_PREFIX . "sitemap_links` (
			  `id` int(11) NOT NULL AUTO_INCREMENT,
			  `link` text NOT NULL,
			  `freq` varchar(10) NOT NULL,
			  `priority` varchar(10) NOT NULL,
			  `store_id` int(11) NOT NULL,
			  `date_added` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
			  PRIMARY KEY (`id`)
			)");
	}
	
	public function uninstall() {
		$this->db->query("DROP TABLE IF EXISTS `" . DB_PREFIX . "sitemap_links`");
	}
	
	public function getLinks($data){		
		$sql = "SELECT * FROM `".DB_PREFIX."sitemap_links` WHERE store_id = '".(int)$data['store_id']."'";
		
		if (!empty($data['search'])) {
			$sql .= " AND (link LIKE '%".$this->db->escape($data['search'])."%')";
		}

		$sql .=  " ORDER BY date_added DESC";
		
		if (isset($data['start']) || isset($data['limit'])) {
			if ($data['start'] < 0) {
				$data['start'] = 0;
			}			

			if ($data['limit'] < 1) {
				$data['limit'] = 20;
			}	

			$sql .= " LIMIT " . (int)$data['start'] . "," . (int)$data['limit'];
		}	

		$query = $this->db->query($sql);
		return $query->rows;
	}
	
	public function getTotalLinks($data){
		$sql = "SELECT count(*) as total FROM `".DB_PREFIX."sitemap_links` WHERE store_id = '".(int)$data['store_id']."'";

		if (!empty($data['search'])) {
			$sql .= " AND (link LIKE '%".$this->db->escape($data['search'])."%')";
		}
		
		$results = $this->db->query($sql);
		return $results->row['total'];
	}

	public function isLinkExists($data){
		$query = $this->db->query("SELECT count(*) as count FROM  `" . DB_PREFIX . "sitemap_links` WHERE `link` = '".$data['link']."' AND store_id = '".(int)$data['store_id']."'");
		if ($query->row['count'] > 0) {
			return true;
		} else {
			return false;
		}
	}

	public function addLink($data){
		$this->db->query("INSERT INTO `".DB_PREFIX."sitemap_links` SET link = '".$this->db->escape(urlencode($data['link']))."', freq = '".$this->db->escape($data['freq'])."', priority = '".$this->db->escape($data['priority'])."', store_id = '".(int)$data['store_id']."'");
	}

	public function deleteLink($id){
		$this->db->query("DELETE FROM `".DB_PREFIX."sitemap_links` WHERE id = '".(int)$id."'");
	}
	
	public function checkInvalidDate($page){
		$sql = "SELECT count(*) as total FROM `".DB_PREFIX.$page."` WHERE date_modified = '0000-00-00 00:00:00' OR date_modified IS NULL";
		$results = $this->db->query($sql);
		return $results->row['total'];
	}
	
	public function updateInvalidDate(){
		$this->db->query("UPDATE `".DB_PREFIX."product` SET date_modified = now() WHERE date_modified = '0000-00-00 00:00:00' OR date_modified IS NULL");
		$this->db->query("UPDATE `".DB_PREFIX."category` SET date_modified = now() WHERE date_modified = '0000-00-00 00:00:00' OR date_modified IS NULL");
	}

	public function getSitemapLinks($sitemapUrl) {
        $links = [];
    
        if (empty($sitemapUrl)) {
            throw new Exception('Sitemap URL cannot be empty.');
        }
    
        try {
            $xmlContent = $this->fetchContent($sitemapUrl);
    
            if ($xmlContent === false) {
                throw new Exception('Unable to fetch the XML file.');
            }
    
            libxml_use_internal_errors(true);
            $xml = simplexml_load_string($xmlContent);
    
            if ($xml === false) {
                throw new Exception('The content is not valid XML.');
            }
    
            if ($xml->getName() === 'sitemapindex') {
                foreach ($xml->sitemap as $sitemap) {
                    $loc = (string)$sitemap->loc;
                    $lastmod = isset($sitemap->lastmod) ? (string)$sitemap->lastmod : null;
    
                    $links[] = [
                        'loc' => $loc,
                        'lastmod' => $lastmod
                    ];
                }
            } else {
                return [];
            }
        } catch (Exception $e) {
            throw new Exception('Error reading the sitemap: ' . $e->getMessage());
        }
    
        return $links;
    }
    
    private function fetchContent($url) {
        // First try file_get_contents
        if (ini_get('allow_url_fopen')) {
            $context = stream_context_create([
                'http' => [
                    'timeout' => 10,
                ]
            ]);
            $content = @file_get_contents($url, false, $context);
    
            if ($content !== false) {
                return $content;
            }
        }
    
        // Fallback to cURL
        if (function_exists('curl_init')) {
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $url);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_TIMEOUT, 10);
            curl_setopt($ch, CURLOPT_FAILONERROR, true);
            curl_setopt($ch, CURLOPT_USERAGENT, 'PHP Sitemap Reader');
    
            $content = curl_exec($ch);
            $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    
            curl_close($ch);
    
            if ($httpCode === 200 && $content !== false) {
                return $content;
            }
        }
    
        // If both methods fail
        throw new Exception('Failed to fetch content using both file_get_contents and cURL.');
    }    

    public function generateSitemaps_1($sitemapUrl, $store_id) {
        /* THIS IS A BACKUP FUNCTION IN CASE generateSitemaps DOESN'T WORK */
        $store_folder_name = ($store_id == 0) ? 'default' : 'store-' . $store_id;
        $sitemapFolder = '../sitemaps/' . $store_folder_name . '/';
    
        if (!file_exists($sitemapFolder)) {
            mkdir($sitemapFolder, 0755, true);
        }
    
        $xmlContent = file_get_contents($sitemapUrl);
        if ($xmlContent === false) {
            throw new Exception('Unable to fetch the sitemap index.');
        }
    
        $xml = new SimpleXMLElement($xmlContent);
        $sitemaps = [];
    
        if (isset($xml->sitemap)) {
            foreach ($xml->sitemap as $sitemap) {
                $loc = (string)$sitemap->loc;
                $sitemaps[] = $loc;
            }
        } else {
            throw new Exception('Provided URL is not a valid sitemap index.');
        }
    
        $newSitemapIndex = new SimpleXMLElement('<sitemapindex xmlns="http://www.sitemaps.org/schemas/sitemap/0.9"/>');
    
        foreach ($sitemaps as $sitemapUrl) {
            $language = 'default';
            $page = '';
            $route = 'misc';
            $directLink = false;
    
            if (strpos($sitemapUrl, '.xml') === false) {
                $parsedUrl = parse_url($sitemapUrl);
                parse_str($parsedUrl['query'], $queryParams);
    
                $language = isset($queryParams['hbxmllang']) ? $queryParams['hbxmllang'] : 'default';
                $page = isset($queryParams['page']) ? $queryParams['page'] : '';
                $route = isset($queryParams['route']) ? explode('/', $queryParams['route'])[3] : 'misc';
            } else {
                if (preg_match('/sitemaps\/([a-z]{2}-[a-z]{2})\//i', $sitemapUrl, $matches)) {
                    $language = $matches[1];
                }
                if (preg_match('/sitemap_(\d+)\.xml$/', $sitemapUrl, $matches)) {
                    $page = $matches[1];
                }
                if (preg_match('/sitemaps\/(?:[a-z]{2}-[a-z]{2}\/)?([a-z]+)_sitemap/i', $sitemapUrl, $matches)) {
                    $route = $matches[1];
                }
            }
    
            $fileName = $page ? $route . '_' . $page . '.xml' : $route . '.xml';  
            $languageFolder = $language === 'default' ? $sitemapFolder : $sitemapFolder . $language . '/';
            if (!file_exists($languageFolder)) {
                mkdir($languageFolder, 0755, true);
            }
    
            $sitemapContent = file_get_contents($sitemapUrl);
            if ($sitemapContent === false) {
                throw new Exception('Unable to fetch the sitemap content from: ' . $sitemapUrl);
            }
    
            $filePath = $languageFolder . $fileName;
            file_put_contents($filePath, $sitemapContent);
    
            $newSitemap = $newSitemapIndex->addChild('sitemap');
            $newSitemap->addChild('loc', HTTPS_CATALOG . 'sitemaps/' . $store_folder_name . '/' . ($language === 'default' ? '' : $language . '/') . $fileName);
            $newSitemap->addChild('lastmod', date('c'));
        }
    
        $newSitemapIndexFile = $sitemapFolder . 'sitemap_index.xml';
        file_put_contents($newSitemapIndexFile, $newSitemapIndex->asXML());
    
        return 'Sitemaps generated successfully!';
    }
    
    public function generateSitemaps($sitemapUrl, $store_id) {
        /* Using multi-cURL for parallel downloads can significantly optimize the process of fetching multiple sitemaps, especially when there are many URLs to process. */
        $store_folder_name = ($store_id == 0) ? 'default' : 'store-' . $store_id;
        $sitemapFolder = '../sitemaps/' . $store_folder_name . '/';
        
        if (!file_exists($sitemapFolder)) {
            mkdir($sitemapFolder, 0755, true);
        }
        
        $xmlContent = file_get_contents($sitemapUrl);
        if ($xmlContent === false) {
            throw new Exception('Unable to fetch the sitemap index.');
        }
    
        $xml = new SimpleXMLElement($xmlContent);
        $sitemaps = [];
    
        if (isset($xml->sitemap)) {
            foreach ($xml->sitemap as $sitemap) {
                $loc = (string)$sitemap->loc;
                $sitemaps[] = $loc;
            }
        } else {
            throw new Exception('Provided URL is not a valid sitemap index.');
        }
    
        $newSitemapIndex = new SimpleXMLElement('<sitemapindex xmlns="http://www.sitemaps.org/schemas/sitemap/0.9"/>');
        $mh = curl_multi_init();
        $curlHandles = [];
    
        foreach ($sitemaps as $sitemapUrl) {
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $sitemapUrl);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            $curlHandles[] = $ch;
            curl_multi_add_handle($mh, $ch);
        }
    
        $running = null;
        do {
            curl_multi_exec($mh, $running);
            usleep(100);
        } while ($running > 0);
    
        foreach ($curlHandles as $index => $ch) {
            $content = curl_multi_getcontent($ch);
            if ($content === false) {
                throw new Exception('Unable to fetch the sitemap content from: ' . $sitemaps[$index]);
            }
            
            $sitemapUrl = $sitemaps[$index];
            $language = 'default';
            $page = '';
            $route = 'misc';
            $directLink = false;
    
            if (strpos($sitemapUrl, '.xml') === false) {
                $parsedUrl = parse_url($sitemapUrl);
                parse_str($parsedUrl['query'], $queryParams);
                $language = isset($queryParams['hbxmllang']) ? $queryParams['hbxmllang'] : 'default';
                $page = isset($queryParams['page']) ? $queryParams['page'] : '';
                $route = isset($queryParams['route']) ? explode('/', $queryParams['route'])[3] : 'misc';
            } else {
                if (preg_match('/sitemaps\/([a-z]{2}-[a-z]{2})\//i', $sitemapUrl, $matches)) {
                    $language = $matches[1];
                }
                if (preg_match('/sitemap_(\d+)\.xml$/', $sitemapUrl, $matches)) {
                    $page = $matches[1];
                }
                if (preg_match('/sitemaps\/(?:[a-z]{2}-[a-z]{2}\/)?([a-z]+)_sitemap/i', $sitemapUrl, $matches)) {
                    $route = $matches[1];
                }
            }
    
            $fileName = $page ? $route . '_' . $page . '.xml' : $route . '.xml';           
            $languageFolder = $language === 'default' ? $sitemapFolder : $sitemapFolder . $language . '/';
            if (!file_exists($languageFolder)) {
                mkdir($languageFolder, 0755, true);
            }
    
            $filePath = $languageFolder . $fileName;
            file_put_contents($filePath, $content);
            
            $newSitemap = $newSitemapIndex->addChild('sitemap');
            $newSitemap->addChild('loc', HTTPS_CATALOG . 'sitemaps/' . $store_folder_name . '/' . ($language === 'default' ? '' : $language . '/') . $fileName);
            $newSitemap->addChild('lastmod', date('c'));
    
            curl_multi_remove_handle($mh, $ch);
            curl_close($ch);
        }
    
        curl_multi_close($mh);
    
        $newSitemapIndexFile = $sitemapFolder . 'sitemap_index.xml';
        file_put_contents($newSitemapIndexFile, $newSitemapIndex->asXML());
    
        return 'Sitemaps generated successfully!';
    }    
	
}
?>