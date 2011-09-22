<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * Script/CSS includes made a little easier
 *
 * Load the library then use js() and cs() to schedule JavaScript and CSS
 * includes, then from your view, echo the html() function's output.
 */
class Scripts {
  
  /// script (js) directory
  public $script_dir = 'assets/script';
  /// css directory
  public $css_dir = 'assets/css';

  /**
   * URL prefix
   * when files are included with their src/href attributes, the prefix is
   * pre-pended to their addresses
   * This defaults to base_url().
   */
  public $prefix = '';

  /// JS to be loaded
  private $scripts = array();
  /// CSS to be loaded
  private $css = array();

  public function Scripts() {
    if (!function_exists('base_url')) {
      $CI = &get_instance();
      $CI->load->helper('url');
    }
    $this->prefix = base_url();
  }

  private function _set_script_dir($dir) {
    $this->script_dir = $dir;
  }
  private function _set_css_dir($dir) {
    $this->css_dir = $dir;
  }

  /**
   * Sets the directory to use for script/CSS includes
   * @param type 'js' or 'css'
   * @param $dir the diretory to use
   * @throws InvalidArgumentException if $type is neither 'css' nor 'js'
   * @warning Don't use absolute paths - use the path relative to the CWD
   * (which should be where your index.php is)
   *
   * TODO: Allow absolute paths, involves some trickery in figuring out where
   * the document root is relative to it.
   */
  public function set_dir($type, $dir) {
    if ($type === 'js') $this->_set_script_dir($dir);
    elseif ($type === 'css') $this->_set_css_dir($dir);
    else throw new InvalidArgumentException("Invalid type: $type");
  }
  private function has_extension($filename, $ext) {
    $substr = substr($filename, strlen($filename) - strlen($ext));
    return $substr === $ext;
  }
  /**
   * Schedules loading of a script/CSS file,
   * @param $type 'js' or 'css'
   * @param $name script/CSS filename
   * @param $exact If FALSE, $name is used in a wildcard
   * @returns TRUE if the file is found and included, otherwise FALSE
   *
   * @throws InvalidArgumentException if $type is neither 'css' nor 'js'
   */
  private function _load_file($type, $name, $exact=false) {
    if (!in_array($type, array('js', 'css')))
      throw new InvalidArgumentException("Invalid type: $type");
    $search_path = ($type === 'js')? $this->script_dir : $this->css_dir;
    $matches = array();
    if (!$exact) {
      $pattern = "{$search_path}/*{$name}";
      if (!$this->has_extension($pattern, '.' . $type))
        $pattern .= '*.' . $type;
      $matches = glob($pattern);
    } else {
      $path = $search_path . '/' . $name;
      if (!$this->has_extension($path, '.' . $type))
        $path .= '.' . $type;
      if (file_exists($path))
        $matches[] = $path;
    }
    if (empty($matches)) return false;
    else {
      if ($type === 'js') $this->scripts = array_merge($this->scripts, $matches);
      else $this->css = array_merge($this->css, $matches);
      return true;
    }
  }
  
  /**
   * Schedules a JavaScript include
   * @param $name the filename
   * @param $exact If FALSE, $name is used in a wildcard
   * @returns TRUE if the file is found and included, otherwise FALSE   *
   */
  public function js($name, $exact=false) {
    return $this->_load_file('js', $name, $exact);
  }

  /**
   * Schedules a CSS include
   * @param $name the filename
   * @param $exact If FALSE, $name is used in a wildcard
   * @returns TRUE if the file is found and included, otherwise FALSE   *
   */
  public function css($name, $exact=false) {
    return $this->_load_file('css', $name, $exact);
  }

  private function _urlify($file) {
    return rtrim($this->prefix, '/') . '/' . ltrim($file, '/');
  }

  /**
   * Gets the HTML to include the scheduled files
   * @param $concat If TRUE, the files are read and their contents written to
   * the page directly, otherwise the script/CSS tags' src and href attributes
   * are set to point to the files
   * @return A string of HTML to go in the head tag
   */
  public function html($concat = false) {
    $output = '';
    foreach($this->scripts as $s) {
      $output .= "<script type='text/javascript'";
      if ($concat) $output .= '>' . file_get_contents($s);
      else $output .= " src='" . $this->_urlify($s) . "'>";
      $output .= '</script>';
      $output .= "\n";
    }
    foreach($this->css as $c) {
      if ($concat) 
        $output .= "<style type='text/css'>" . file_get_contents($c) . "</style>";
      else {
        $output .= "<link rel='stylesheet' type='text/css' href='";
        $output .= $this->_urlify($c) . "'>";
      }
      $output .= "\n";
    }
    return $output;
  }
}
