<?
include_once('simple_html_dom.php');
include_once('SDbBase.php');
if(isset($_POST['do']) == "table")
    {
        ProxyParser::getTable();
    }
class ProxyParser {
        static function curl_zapros($url){
			$ch = curl_init();
			curl_setopt($ch, CURLOPT_URL, $url);
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1); // возвращает строку
			curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1); // редирект
			curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows; U; Windows NT 5.1; ru; rv:1.9.0.1) Gecko/2008070208');
			$out = curl_exec($ch);
			$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
			$error = curl_error($ch);
			curl_close($ch);
			return $out;
		}
        
		static function timeconvert($string) {
			$date = new DateTime();
			if (preg_match("/мин/i", $string)) { //возвращаем минуты
			   $result = $date->getTimestamp() - preg_replace( '/[^0-9]/', '', $string) * 60;
			   return $result;
			} elseif (preg_match("/сек/i", $string)) { //возвращаем секунды
				(int)$result = $date->getTimestamp() - preg_replace( '/[^0-9]/', '', $string);
				return $result;
			}
		}
		static function parsing() {
			$link = SDb::connect();
			$date = new DateTime();
			while($i<100) {
				$out = ProxyParser::curl_zapros("http://hideme.ru/proxy-list/?start=" . $i . "#list");
				$html = str_get_html($out);		
				foreach ($html->find('tr') as $div) {
				    if ($i < 100) {
				    if ($div->find('td', 0) != null){
				        SDb::insert($link, strip_tags($div->find('td', 0)), strip_tags($div->find('td', 1)), iconv("Windows-1251", "UTF-8", strip_tags($div->find('td', 3))), iconv("Windows-1251", "UTF-8", strip_tags($div->find('td', 4))), iconv("Windows-1251", "UTF-8", strip_tags($div->find('td', 5))), $date->getTimestamp(), ProxyParser::timeconvert(iconv("Windows-1251", "UTF-8",strip_tags($div->find('td', 6)))));
				        $i++;
				        }
				    }
				}	
            }
		}
        
        static function getTable(){
                ProxyParser::parsing();
                $link = SDb::connect();
	           $query= SDb::read($link);
	           echo "</br><table><tr><th>Порт</th><th>Кол-во ip</th><th>Ip</th></tr>";
	           foreach($query as $a){
		          echo "<tr><td>".$a['port']."</td><td>".$a['kol']."</td><td>".$a['ip']."</td></tr>";
	           }
	           echo "</table>";
            
        }
}
    ?>