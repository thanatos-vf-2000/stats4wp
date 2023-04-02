<?php
/**
 * @package  STATS4WPPlugin
 * @version 1.4.2
 */
namespace STATS4WP\Api\Callbacks;

use STATS4WP\Core\BaseController;

class ManagerCallbacks extends BaseController
{
    public function checkboxSanitize($input)
    {
        $output = array();

        $all_defaults = $this->loadPHPConfig(STATS4WP_PATH . 'assets/defaults.php');

        foreach ($this->managers as $key => $value) {
            if (isset($all_defaults[$key])) {
                $config = $all_defaults[$key];
                if ($config['type'] == "checkboxField") {
                    $output[$key] = isset($input[$key]) ? true : false;
                } else {
                    $output[$key] = $input[$key];
                }
            }
        }

        return $output;
    }

    public function adminIndexSectionManager()
    {
        echo __('Manage the Sections and Features of this Plugin by activating the checkboxes from the following list.', 'stats4wp');
    }


    public function checkboxField($args)
    {
        $name = $args['label_for'];
        $classes = $args['class'];
        $option_name = $args['option_name'];
        $checkbox = get_option($option_name);
        $checked = isset($checkbox[$name]) ? ($checkbox[$name] ? true : false) : false;

        echo '<div class="' . esc_attr($classes) . '"><input type="checkbox" id="' . esc_attr($name) . '" name="' . esc_attr($option_name) . '[' . esc_attr($name) . ']" value="1" class="" ' . ( esc_attr($checked) ? 'checked' : '') . '><label for="' . esc_attr($name) . '"><div></div></label></div>';

        if ($args['message'] <> '') {
            if ($args['link'] <> '') {
                echo '<p class="description">' . esc_html($args['message']) . ' <a href="' . esc_url($args['link']) .'" target="_blank">' . __('Link', 'stats4wp').'</a></p>';
            } else {
                echo '<p class="description">' . esc_html($args['message']) . '</p>';
            }
        }
    }

    public function listField($args)
    {
        $name = $args['label_for'];
        $classes = $args['class'];
        $option_name = $args['option_name'];

        echo '<div class="' . esc_attr($classes) . '"><select id="' . esc_attr($name) . '" name="' . esc_attr($option_name) . '[' . esc_attr($name) . ']">';
        foreach ($args['choices'] as $value => $label) :
            $opt = ($args['value'] == $value) ? 'selected' : '';
            echo '<option value="' . esc_attr($value) . '" ' . esc_attr($opt) . '>' . esc_html($label) . '</option>';
        endforeach;
        ?>
            </select>
        </div>
        <?php
        if ($args['message'] <> '') {
            echo '<p class="description">' . esc_html($args['message']) . '</p>';
        }
    }

    public function ImageField($args)
    {
        $name = $args['label_for'];
        $classes = $args['class'];
        $option_name = $args['option_name'];
        echo '<div class="' . esc_attr($classes) . '">
				<input id="upload_image" type="text" size="36" name="' . esc_attr($option_name) . '[' . esc_attr($name) . ']" value="' . esc_attr($args['value']) . '" /> 
				<input id="upload_image_button" for="' . esc_attr($option_name) . '[' . esc_attr($name) . ']" class="button" type="button" value="' . __('Upload Menu', 'stats4wp') . '" />
				<br>
				<img id="imageBox" name="' . esc_attr($option_name) . '[' . esc_attr($name) . ']" style="height: ' . esc_attr($args['height']) . '; width: ' . esc_attr($args['width']) . ';" src="' . esc_url($args['value']) . '">
			</div>';
    }

    public function ColorField($args)
    {
        $name = $args['label_for'];
        $classes = $args['class'];
        $option_name = $args['option_name'];
        echo '<p>
			<label for="' . esc_attr($option_name) . '[' . esc_attr($name) . ']" style="display:block;">' . __('Color:', 'stats4wp') .'</label> 
			<input class="color-picker" id="' . esc_attr($option_name) . '[' . esc_attr($name) . ']" name="' . esc_attr($option_name) . '[' . esc_attr($name) . ']" type="text" value="' . esc_attr($args['value']) . '" />
		</p>';
    }

    public function TextField($args)
    {
        $name = $args['label_for'];
        $classes = $args['class'];
        $option_name = $args['option_name'];
        echo '<div class="' . esc_attr($classes) . '">
				<input id="'. esc_attr($name) .'" type="text" size="50" name="' . esc_attr($option_name) . '[' . esc_attr($name) . ']" value="' . esc_attr($args['value']) . '" /> 
			</div>';
    }

    private function loadPHPConfig($path)
    {
        if (! file_exists($path)) {
            return array();
        }
            $content = require $path;
            return $content;
    }
}
