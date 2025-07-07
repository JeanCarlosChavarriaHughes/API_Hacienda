<?php
/**
 * Validate a base64-encoded XML string against an XSD schema.
 *
 * @return array Validation result with success status and errors (if any).
 */
function validateXML()
{
    $base64Xml = params_get('base64Xml');
    //$xsdPath = params_get('xsdPath') ?? '/XSD/FacturaElectronica_V4.4-noSign.xsd';
    $xsdPath = 'xsd/FacturaElectronica_V4.4-noSign.xsd'; // need to provide type of XSD dinamycally

    $xmlString = base64_decode($base64Xml);

    if ($xmlString === false) {
        return array(
            'success' => false,
            'errors' => array('Failed to decode base64 XML.')
        );
    }

    $dom = new DOMDocument();

    // Load the XML string
    if (!$dom->loadXML($xmlString)) {
        return array(
            'success' => false,
            'errors' => array('Failed to load XML. Ensure it is well-formed.')
        );
    }

    // Validate the XML against the XSD
    libxml_use_internal_errors(true); // Capture errors
    $isValid = $dom->schemaValidate($xsdPath);
    $errors = libxml_get_errors();
    libxml_clear_errors();

    if ($isValid) {
        return array(
            'success' => true,
            'errors' => array()
        );
    } else {
        // Format errors for better readability
        $formattedErrors = array_map(function ($error) {
            return trim($error->message) . " (Line: {$error->line}, Column: {$error->column})";
        }, $errors);

        return array(
            'success' => false,
            'errors' => $formattedErrors
        );
    }
}
