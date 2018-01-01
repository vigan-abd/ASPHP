<?php
namespace ASPHP\Core\Serialization\XML;
require_once __DIR__.'/Serializer.php';
use \XML_Serializer;

class XmlSerializer
{
	public static function Serialze($data)
	{
		$options = [
			XML_SERIALIZER_OPTION_INDENT      => '    ',
			XML_SERIALIZER_OPTION_LINEBREAKS  => "\n",
			XML_SERIALIZER_OPTION_DEFAULT_TAG => 'unnamedItem',
			XML_SERIALIZER_OPTION_TYPEHINTS   => true
		];

		$serializer = new XML_Serializer($options);
		$result = $serializer->serialize($data);
		if( $result === true ) 
			$xml = $serializer->getSerializedData();
		else
			$xml = "";

		return $xml;
	}
}
?>