<?php

if ( !defined('ABSPATH') )
    exit; // Shhh

/**
 * List Customizer Fields.
 * 
 * @since 2.0.0
 * @package ListForArticles\CustomizerFields
 */

class LFA_List_Customizer_Fields {

    /**
     * Convert an array of attributes to html attributes form (name="value).
     * 
     * @since 2.0.0
     * @param array $attrs Attributes (name => value or name=> array(value, value))
     * @param string $separator Separator when more than one value is present
     * @return string HTML attributes
     */
    public static function attrs($attrs, $separator = ' ')
    {
	if ( !is_array($attrs) || empty($attrs) )
	    return;
	// HTML Container
	$html_attrs = "";
	foreach ( $attrs as $name => $value ):
	    $html_attrs .= sprintf('%s="%s" ', $name, esc_attr(implode($separator, (array) $value)));
	endforeach;

	return $html_attrs;
    }

    /**
     * Label.
     * 
     * @since 2.0.0
     * @param string $id ID
     * @param string $label Label
     * @param array $attrs Attributes
     * @return string Label HTML element
     */
    public static function label($id, $label, $attrs)
    {
	return sprintf('<label for="%s" %s>%s</label>', $id, self::attrs($attrs), $label);
    }

    /**
     * Input.
     * 
     * @since 2.0.0
     * @param array $args  Arguments
     * @return string Input element
     */
    public static function input($args)
    {
	$args = wp_parse_args($args, array( 'id' => '', 'name' => '', 'default' => '', 'value' => '', 'class' => array(), 'attrs' => array() ));
	extract($args);

	$attrs['class'] = array_merge(isset($attrs['class']) ? $attrs['class'] : array(), $class);

	$html_element = '%s<input type="%s" id="%s" name="%s" value="%s" %s>';
	return sprintf($html_element, self::label($id, $label, array()), $type, $id, $name, empty($value) ? $default : $value, self::attrs($attrs));
    }

    /**
     * Textarea.
     * 
     * @since 2.0.0
     * @param type $args Arguments
     * @return string
     */
    public static function textarea($args)
    {
	$args = wp_parse_args($args, array( 'id' => '', 'name' => '', 'default' => '', 'value' => '', 'class' => array(), 'attrs' => array() ));
	extract($args);

	$attrs['class'] = array_merge(isset($attrs['class']) ? $attrs['class'] : array(), $class);

	$html_element = '%s<textarea id="%s" name="%s" %s>%s</textarea>';
	return sprintf($html_element, self::label($id, $label, array()), $id, $name, self::attrs($attrs), empty($value) ? $default : $value);
    }

    /**
     * Select.
     * 
     * @since 2.0.0
     * @param type $args
     * @return type
     */
    public static function select($args)
    {
	$args = wp_parse_args($args, array( 'id' => '', 'name' => '', 'default' => '', 'value' => '', 'options' => array(), 'class' => array(), 'attrs' => array() ));
	extract($args);

	$attrs['class'] = array_merge(isset($attrs['class']) ? $attrs['class'] : array(), $class);
	$options_list = '';
	foreach ( $options as $option ):
	    $options_list .= sprintf('<option value="%s" %s>%s</option>', $option['value'], selected(empty($value) ? $default : $value, $option['value'], false), $option['label']);
	endforeach;

	$html_element = '%s<select id="%s" name="%s" %s>%s</select>';
	return sprintf($html_element, self::label($id, $label, array()), $id, $name, self::attrs($attrs), $options_list);
    }

    /**
     * Color field.
     * 
     * @since 2.0.0
     * @param array $args Arguments
     * @return string Colorpicker element
     */
    public static function color($args)
    {
	$args = wp_parse_args($args, array( 'id' => '', 'default' => '', 'value' => '', 'class' => array(), 'attrs' => array() ));
	$args['type'] = 'text';
	$args['class'][] = 'lfa-color-field';

	return self::input($args);
    }

    /**
     * Text field.
     * 
     * @since 2.0.0
     * @param type $args
     * @return string Text field
     */
    public static function text($args)
    {
	$args = wp_parse_args($args, array( 'id' => '', 'default' => '', 'value' => '', 'class' => array(), 'attrs' => array() ));
	$args['type'] = 'text';

	return self::input($args);
    }

}
