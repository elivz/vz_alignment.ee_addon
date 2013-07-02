<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * VZ Alignment Class
 *
 * @author    Eli Van Zoeren <eli@elivz.com>
 * @copyright Copyright (c) 2009-2013 Eli Van Zoeren
 * @license   http://creativecommons.org/licenses/by-sa/3.0/ Attribution-Share Alike 3.0 Unported
 */

class Vz_alignment_ft extends EE_Fieldtype {

    var $has_array_data = TRUE;

    public $info = array(
        'name'      => 'VZ Alignment',
        'version'   => '0.9.0',
    );

    /**
     * Fieldtype Constructor
     *
     */
    function __construct()
    {
        parent::__construct();

        ee()->lang->loadfile('vz_alignment');

        // Initialize the cache
        if ( ! isset(ee()->session->cache['vz_alignment']) )
        {
            ee()->session->cache['vz_alignment'] = array();
        }
        $this->cache =& ee()->session->cache['vz_alignment'];
    }


    /**
     * Install Fieldtype
     *
     */
    function install()
    {
        // Default field settings
        return array(
            'direction'   => 'both',
            'select_mode' => 'single'
        );
    }

    protected function _directions()
    {
        return array(
            'both'       => lang('direction_both'),
            'vertical'   => lang('direction_vertical'),
            'horizontal' => lang('direction_horizontal')
        );
    }

    protected function _modes()
    {
        return array(
            'single'   => lang('mode_single'),
            'multiple' => lang('mode_multiple')
        );
    }


    /**
     * Include the JS and CSS files,
     * but only the first time
     *
     */
    private function _include_jscss()
    {
        if ( ! isset($this->cache['css']) )
        {
            ee()->cp->add_to_head('<style type="text/css">
                .vz_alignment_table label { display: block; width: 14px; height: 14px; background: #f7fafc; border: 1px solid #D1D5DE; -moz-border-radius: 9px; border-radius: 9px; -webkit-box-shadow:inset 0 2px 3px rgba(255,255,255,0.8); -moz-box-shadow:inset 0 2px 3px rgba(255,255,255,0.8); box-shadow:inset 0 2px 3px rgba(255,255,255,0.8); text-indent: 100%; overflow: hidden; }
                .vz_alignment_table label:hover, .vz_alignment_table label:focus { background: #ffffff; -webkit-box-shadow: 0 0 5px #abd9f4; -moz-box-shadow: 0 0 5px #abd9f4; box-shadow: 0 0 5px #abd9f4; }
                .vz_alignment_table input:checked + label { background-color: #b0b4b9; background-image: -webkit-gradient(linear, left top, right bottom, from(#aaaeb3), to(#b6babf)); background-image: -webkit-linear-gradient(left top, #aaaeb3, #b6babf); background-image: -moz-linear-gradient(left top, #aaaeb3, #b6babf); background-image: -ms-linear-gradient(left top, #aaaeb3, #b6babf); background-image: linear-gradient(left top, #aaaeb3, #b6babf); border-color:#a7b4c2; -webkit-box-shadow:inset 0 1px rgba(0,0,0,0.1); -moz-box-shadow:inset 0 1px 3px rgba(0,0,0,0.1); box-shadow:inset 0 1px 3px rgba(0,0,0,0.1); }
                .vz_alignment_table input { position:absolute; left:-9999px; }
            </style>');

            $this->cache['css'] = TRUE;
        }
    }


    // --------------------------------------------------------------------


    /**
     * Display Field Settings
     */
    function display_settings($data)
    {
        $settings_array = $this->_get_settings($data);

        foreach ($settings_array as $settings_row)
        {
            ee()->table->add_row($settings_row);
        }
    }


    /**
     * Display Cell Settings
     */
    function display_cell_settings($cell_settings)
    {
        return $this->_get_settings($cell_settings);
    }


    /**
     * Create the settings ui
     */
    private function _get_settings($settings)
    {
        ee()->load->helper('form');

        $settings = array_merge($this->settings, $settings);

        $row1 = array(
            lang('direction_label_cell'),
            form_dropdown('direction', $this->_directions(), $settings['direction'])
        );

        $row2 = array(
            lang('mode_label_cell'),
            form_dropdown('select_mode', $this->_modes(), $settings['select_mode'])
        );

        return array( $row1, $row2 );
    }


    // --------------------------------------------------------------------


    /*
     * Save field settings
     */
    function save_settings()
    {
        return array(
            'direction'   => ee()->input->post('direction'),
            'select_mode' => ee()->input->post('select_mode')
        );
    }


    // --------------------------------------------------------------------

    /**
     * Display Field
     */
    function display_field($field_data)
    {
        return $this->_get_display($this->field_name, $field_data);
    }


    /**
     * Display Cell
     */
    function display_cell($cell_data)
    {
        return $this->_get_display($this->cell_name, $cell_data);
    }


    /**
     * Generate HTML for field on Publish page
     */
    private function _get_display($name, $data)
    {
        // Include styling
        $this->_include_jscss();

        // Process data back into array
        $data = $this->pre_process($data);

        // Apply the field settings
        $input_type = $this->settings['select_mode'] == 'single' ? 'radio' : 'checkbox';
        $vertical_axis = $this->settings['direction'] == 'horizontal' ? array('center') : array('top', 'center', 'bottom');
        $horizontal_axis = $this->settings['direction'] == 'vertical' ? array('center') : array('left', 'center', 'right');

        // Build the HTML
        $output = '<table class="vz_alignment_table">';
        foreach($vertical_axis as $vertical_step)
        {
            $output .= '<tr>';
            foreach($horizontal_axis as $horizontal_step)
            {
                // Is it already selected?
                $value = $horizontal_step.'-'.$vertical_step;
                $checked = in_array($value, $data) ? 'checked' : '';

                $output .= '<td>';
                $output .= '<input type="'.$input_type.'" id="'.$name.'_'.$value.'" name="'.$name.'[]" value="'.$value.'" '.$checked.'>';
                $output .= '<label for="'.$name.'_'.$value.'">'.$value.'</label>';
                $output .= '</td>';
            }
            $output .= '</tr>';
        }
        $output .= '</table>';

        return $output;
    }


    // --------------------------------------------------------------------


    /*
     * Save field
     */
    function save($data)
    {
        return implode('|', $data);
    }

    /**
     * Save Cell
     */
    function save_cell($data)
    {
        return $this->save($data);
    }


    // --------------------------------------------------------------------


    /*
     * Convert the stored string back to an array
     */
    function pre_process($data)
    {
        $data = explode('|', $data);
        if ( ! is_array($data)) $data = array();
        return $data;
    }


    /**
     * Display Tag
     */
    function replace_tag($field_data, $params=array(), $tagdata=FALSE)
    {
        if ( ! $tagdata) // Single tag
        {
            $separator = isset($params['separator']) ? $params['separator'] : '-';
            $separator = str_replace('SPACE', ' ', $separator);
            $multiple_separator = isset($params['multiple_separator']) ? $params['multiple_separator'] : ' ';
            $multiple_separator = str_replace('SPACE', ' ', $multiple_separator);

            $return_array = array();
            foreach ($field_data as $item)
            {
                list($horizontal, $vertical) = explode('-', $item);
                if ($this->settings['direction'] == 'vertical')
                {
                    $return_array[] = $vertical;
                }
                elseif ($this->settings['direction'] == 'horizontal')
                {
                    $return_array[] = $horizontal;
                }
                else
                {
                    $return_array[] = $horizontal.$separator.$vertical;
                }
            }

            return implode($multiple_separator, $return_array);
        }
        else // Tag pair
        {
            $return_array = array();
            foreach ($field_data as $item)
            {
                list($horizontal, $vertical) = explode('-', $item);

                $return_array[] = array(
                    'horizontal' => $horizontal,
                    'vertical'   => $vertical
                );
            }

            $output = ee()->TMPL->parse_variables($tagdata, $return_array);

            return $output;
        }
    }

}

/* End of file ft.vz_alignment.php */