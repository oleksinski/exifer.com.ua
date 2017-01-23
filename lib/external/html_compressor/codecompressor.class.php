<?

/**
 * Generic class for compressing website sourcecode.
 *
 * This class requires PHP 5.
 *
 * Copyright 2009 by Simon Wippich, www.wippich.org
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU Lesser General Public License as published
 * by the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 * GNU Lesser General Public License for more details.
 *
 * You should have received a copy of the GNU Lesser General Public License
 * along with this program. If not, see <http://www.gnu.org/licenses/>.
 *
 * @version 1.0
 * @author Simon Wippich <development@wippich.org>
 * @copyright Copyright (c) 2009, Simon Wippich
 * @license http://www.gnu.org/licenses/ GNU Lesser General Public License
 */
class CodeCompressor{

	/**
	 * Method for compressing HTML-sourcecode.
	 *
	 * @access public
	 * @param $htmlSourceCode String The original HTML-sourcecode
	 * @return String The compressed HTML-sourcecode
	 */
	final public static function compressHtml(
		$htmlSourceCode,
		$removeComments = true
	){
		// Initialize a returning variable
		$returnValue = '';
		try {
			// Check if the given parameter matches datatype string
			if(is_string($htmlSourceCode)){
				// Exclude pre- or code-tags
				_preg_match_all(
					'#(<(?:code|pre|script|textarea).*>[^<]+</(?:code|pre|script|textarea)>)#',
					$htmlSourceCode,
					$pre
				);
				// Remove all pre- or code-tags
				$htmlSourceCode = _preg_replace(
					'#<(?:code|pre|script|textarea).*>[^<]+</(?:code|pre|script|textarea)>#',
					'#pre#',
					$htmlSourceCode
				);
				// Remove HTML-comments if required
				if($removeComments === true){
					$htmlSourceCode = _preg_replace(
						'/<!--[^\[](.|\s)*?[^\]]-->/',
						'',
						$htmlSourceCode
					);
				}
				// Remove new lines, spaces and tabs
				$htmlSourceCode = _preg_replace(
					'/[\r\n\t]+/',
					' ',
					$htmlSourceCode
				);
				$htmlSourceCode = _preg_replace(
					'/>[\s]+</',
					'><',
					$htmlSourceCode
				);
				$htmlSourceCode = _preg_replace(
					'/[\s]+/',
					' ',
					$htmlSourceCode
				);
				if(!empty($pre[0])){
					foreach($pre[0] as $tag){
						if(_preg_match('#(<(?:script).*>[^<]+</(?:script)>)#', $tag)) {
							$tag = "\n".Text::removeExtraNL($tag,1,1)."\n";
						}
						// Return pre- and code-tags
						$htmlSourceCode = _preg_replace(
							'!#pre#!',
							$tag,
							$htmlSourceCode,
							1
						);
					}
				}
				// Remove preceding and trailing spaces and write
				// the processed sourcecode into the returning variable
				$returnValue = _trim($htmlSourceCode);
				// Delete the original sourcecode
				unset($htmlSourceCode);
			} else{
				// Throw an exception in case of an invalid method parameter
				throw new Exception(
					'Method "' .
					__FUNCTION__ .
					'" of class "CodeCompressor" reported an error: ' .
					'Invalid parameter!'
				);
			}
		} catch(Exception $e){
			// Catch all occuring exceptions form the preceding codeblock
			// and write the errormessages into the returning variable
			$returnValue = $e->getMessage();
		}
		// Return the filled variable
		return $returnValue;
	}

}
