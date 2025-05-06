<?php
include_once($SERVER_ROOT.'/classes/Manager.php');

class SitemapXMLManager extends Manager {
    private $host;
    private $database;
    private $port;
    private $username;

    public function __construct() {

        $this->host = MySQLiConnectionFactory::$SERVERS[0]['host'];
        $this->database = MySQLiConnectionFactory::$SERVERS[0]['database'];
        $this->port = MySQLiConnectionFactory::$SERVERS[0]['port'];
    }

    public function generateSitemap() {
        global $CLIENT_ROOT, $SERVER_ROOT;

    $conn = MySQLiConnectionFactory::getCon("readonly");
    $baseUrl = rtrim($CLIENT_ROOT, '/');

    $xml = "<?xml version=\"1.0\" encoding=\"UTF-8\"?>\n";
    $xml .= "<urlset xmlns=\"http://www.sitemaps.org/schemas/sitemap/0.9\">\n";

    $xml .= $this->generateCollectionsSitemap($conn, $baseUrl);
    $xml .= $this->generateChecklistsSitemap($baseUrl);
    $xml .= $this->generateProjectsSitemap($baseUrl);
    $xml .= $this->generateExsiccataSitemap($conn, $baseUrl);
    $xml .= $this->generateTaxaSitemap($conn, $baseUrl);

    $xml .= "</urlset>\n";

    $conn->close();

    $outputDir = $SERVER_ROOT . '/content/sitemaps';
    $timestamp = date('Y-m-d_H-i-s');
    $outputFile = "{$outputDir}/sitemap-{$timestamp}.xml";

    if (!is_dir($outputDir)) {
        mkdir($outputDir, 0755, true);
    }

    file_put_contents($outputFile, $xml);

    return true;
    }

    private function generateCollectionsSitemap($conn, $baseUrl) {
        $xml = '';
        $sql = "SELECT collid, initialtimestamp AS timestamp FROM omcollections";
        $rs = $conn->query($sql);
        while ($row = $rs->fetch_assoc()) {
            $xml .= "  <url>\n";
            $xml .= "    <loc>{$baseUrl}/collections/misc/collprofiles.php?collid={$row['collid']}</loc>\n";
            $xml .= "    <lastmod>" . date("Y-m-d", strtotime($row['timestamp'])) . "</lastmod>\n";
            $xml .= "  </url>\n";
        }
        return $xml;
    }

    private function generateChecklistsSitemap($baseUrl) {
        return "  <url>\n" .
               "    <loc>{$baseUrl}/checklists/index.php</loc>\n" .
               "  </url>\n";
    }

    private function generateProjectsSitemap($baseUrl) {
        return "  <url>\n" .
               "    <loc>{$baseUrl}/projects/index.php</loc>\n" .
               "  </url>\n";
    }

    private function generateExsiccataSitemap($conn, $baseUrl) {
        $xml = '';
        $sql = "SELECT ometid, initialtimestamp FROM omexsiccatititles";
        $rs = $conn->query($sql);
        while ($row = $rs->fetch_assoc()) {
            $xml .= "  <url>\n";
            $xml .= "    <loc>{$baseUrl}/collections/exsiccati/index.php?ometid={$row['ometid']}</loc>\n";
            $xml .= "    <lastmod>" . date("Y-m-d", strtotime($row['initialtimestamp'])) . "</lastmod>\n";
            $xml .= "  </url>\n";
        }
        return $xml;
    }

    private function generateTaxaSitemap($conn, $baseUrl) {
        $xml = '';
        $sql = "SELECT tid, modifiedtimestamp, initialtimestamp FROM taxa WHERE rankid <= 180";
        $rs = $conn->query($sql);
        while ($row = $rs->fetch_assoc()) {
            $xml .= "  <url>\n";
            $xml .= "    <loc>{$baseUrl}/taxa/index.php?tid={$row['tid']}</loc>\n";
            $timestamp = !empty($row['modifiedtimestamp']) ? $row['modifiedtimestamp'] : $row['initialtimestamp'];
            if (!empty($timestamp)) {
                $xml .= "    <lastmod>" . date("Y-m-d", strtotime($timestamp)) . "</lastmod>\n";
            }
            $xml .= "  </url>\n";
        }
        return $xml;
    }

    public function __destruct() {
        parent::__destruct();
    }
}