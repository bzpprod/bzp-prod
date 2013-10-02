<?php
/**
 * @class UtilComponent
 * A collection of simple but usefull functions to be used in controllers
 *
 * @author: Carlos Rios
 * @version: 1.0
 */
class UtilComponent extends Component {

	var $name = 'Util';
	
	public function slugify($string, $replace = null, $delimiter = '-', $maxPhraseLen = 5, $minWordLen = 3)
	{
		if (!empty($replace))
		{
			$string = str_replace((array)$replace, ' ', $string);
		}
		
		// replace non letter or digits by -
		$string = preg_replace('~[^\\pL\d]+~u', $delimiter, $string);
		
		// trim
		$string = trim($string, '-');
		
		// transliterate
		$string = iconv('UTF-8', 'ASCII//TRANSLIT', $string);

		// lowercase
		$string = strtolower($string);

		// remove unwanted characters
		$string = preg_replace('~[^-\w]+~', '', $string);

		// http://www.searchenginejournal.com/seo-best-practices-for-url-structure/7216/
		if ($maxPhraseLen > 0)
		{
			$string = explode($delimiter, $string);
			$stringNew = array();
			foreach ($string as $str)
			{
				if (strlen($str) > $minWordLen)
				{
					$stringNew[] = $str;
				}
			}
			
			// If the new array is empty, use the old one
			if (count($stringNew) == 0)
			{
				$stringNew = $string;
			}
			else
			{
				$stringNew = array_slice($string, 0, $maxPhraseLen);
			}
			
			$string = implode($delimiter, $string);
		}
		
		if (empty($string))
		{
			return 'n-a';
		}
		return $string;
	}

}
