<?

Class logSummary {

	public $views;
	public $urls;
	public $traffic;
	public $crawlers = [];
	public $statusCodes = [];
	private $urlsData = [];

	function __construct() {
		$this->views = 0;
		$this->urls = 0;
		$this->traffic = 0;
		$this->crawlers = array("Google" => 0, "Bing" => 0, "Baidu" => 0, "Yandex" => 0);
		$this->statusCodes = array("200" => 0);
	}

	function processLogString(string $logString) : void {
		$explodedLogString = explode("\"", $logString);
		$explodedLogString[1] = explode(" ", $explodedLogString[1]);
		$explodedLogString[2] = explode(" ", $explodedLogString[2]);
		$this->views++;
		$this->traffic += (int) $explodedLogString[2][2];
		$this->urlsData[$explodedLogString[1][1]] = 1;
		$this->urls = count($this->urlsData);
		if(array_key_exists($explodedLogString[2][1], $this->statusCodes)) {
			$this->statusCodes[$explodedLogString[2][1]]++;
		} else {
			$this->statusCodes[$explodedLogString[2][1]] = 1;
		}
		$this->findCrawlerFingerprint($logString);
	}

	function findCrawlerFingerprint(string $logString) : void {
		$loweredLogString = strtolower($logString);
		if(strpos($loweredLogString, "googlebot") > 0) {
			$this->crawlers["Google"]++;
		}
		else if(strpos($loweredLogString, "bingbot") > 0) {
			$this->crawlers["Bing"]++;
		}
		else if(strpos($loweredLogString, "baiduspider") > 0) {
			$this->crawlers["Baidu"]++;
		}
		else if(strpos($loweredLogString, "yandexbot") > 0) {
			$this->crawlers["Yandex"]++;
		}
	}

	function printData() : void {
		echo json_encode($this);
	}
}

$myLogSummary = new logSummary;

$fileStream=fopen("access.log","r");

while($line=fgets($fileStream)) {
        $myLogSummary->processLogString($line);
}

$myLogSummary->printData();
?>