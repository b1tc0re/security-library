<?php namespace DeftCMS\Components\b1tc0re\Security\Captcha\Handlers;

use DeftCMS\Components\b1tc0re\Security\Captcha\Interfaces\IHandler;

defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Работа с системной капчи CodeIgniter
 * Working with CodeIgniter system captcha
 *
 * @package	    DeftCMS
 * @category    Core
 * @author	    b1tc0re
 * @copyright   (c) 2018-2020, DeftCMS (http://deftcms.ru/)
 * @since	    Version 0.0.9
 */
class SystemCaptcha implements IHandler
{
    /**
     * Текст капчи
     * Captcha text
     *
     * @var string
     */
    protected $word;

    /**
     * Длина картинки
     * Captcha length
     *
     * @var int
     */
    protected $img_width = 200;

    /**
     * Высота картинки
     * Captcha height
     *
     * @var int
     */
    protected $img_height = 60;

    /**
     * Путь к шрифту для капчи
     * Font path for captcha
     *
     * @var string
     */
    protected $font_path;

    /**
     * Использовать обводку для картинки
     * Use stroke for captcha
     *
     * @var bool
     */
    protected $use_border = false;

    /**
     * Количество символов для нанесения на капчу
     * Number of characters to apply to captcha
     *
     * @var int
     */
    protected $word_length = 8;

    /**
     * Размер шрифта
     * Font size
     *
     * @var int
     */
    protected $font_size = 16;

    /**
     * Символы из которых состоит капча
     * Symbols of which captcha consists
     *
     * @var string
     */
    protected $pool = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';

    /**
     * Цветовая схема для рисунка капчи
     * The color scheme for the captcha
     *
     * @var array
     */
    protected $colors = [
        'background'	=> [255,255,255],
        'border'	    => [221,221,221],
        'text'		    => [0,0,0],
        'grid'		    => [0,0,0]
    ];

    /**
     * Цвета которые нужно сгенерировать случайно
     * Colors to be randomly generated
     *
     * @var array
     */
    protected $random_color = [
        'grid' => 60,
        'text' => 30,
    ];

    /**
     * Название сессии для хранение текста капчи
     * Session name for captcha text storage
     *
     * @var string
     */
    protected $flash_session_name = '_captcha';

    /**
     * Инициализация параметров
     * Parameter initialization
     *
     * @param array $params
     * @return $this
     */
    protected function initialize(array $params)
    {
        foreach ($params as $prop => $value)
        {

            if( property_exists($this, $prop) && is_array($value) )
            {
                foreach ($value as $a_name => $a_value)
                {
                    if( array_key_exists($a_name, $this->{$prop}) ) {
                        $this->{$prop}[$a_name] = $a_value;
                    }
                }
            }
            elseif( property_exists($this, $prop) ) {
                $this->{$prop} = $value;
            }
        }

        if( $this->font_path === '' ||  !file_exists($this->font_path) ) {
            $this->font_path = realpath(\DeftCMS\Engine::$DT->config->item('storage_path') . '/fonts/captcha.ttf');
        }

        return $this;
    }

    /**
     * Сгенерировать изображение капчи
     * Generate captcha image
     *
     * @param array $params
     */
    public function getCaptcha(array $params)
    {
        $this->initialize($params);

        if ( !extension_loaded('gd') ) {
            \DeftCMS\Engine::$Log->critical('Расширение php-gd не загружено или не установлено');
            return;
        }

        if( empty($this->word) ) {
            $this->word = $this->_getWorld();
        }

        // -----------------------------------
        // Determine angle and position
        // -----------------------------------
        $length	= strlen($this->word);
        $angle	= ($length >= 6) ? mt_rand(-($length-6), ($length-6)) : 0;
        $x_axis	= mt_rand(6, (360/$length)-16);
        $y_axis = ($angle >= 0) ? mt_rand($this->img_height, $this->img_width) : mt_rand(6, $this->img_height);

        // -----------------------------------
        // Determine angle and position
        // -----------------------------------

        // Create image
        // PHP.net recommends imagecreatetruecolor(), but it isn't always available
        $im = function_exists('imagecreatetruecolor')
            ? imagecreatetruecolor($this->img_width, $this->img_height)
            : imagecreate($this->img_width, $this->img_height);

        // -----------------------------------
        //  Assign colors
        // ----------------------------------

        /* RAND */
        $red    = mt_rand(50, 100);
        $green  = mt_rand(50, 100);
        $blue   = mt_rand(50, 100);

        foreach (array_keys($this->colors) as $key)
        {
            if( array_key_exists($key, $this->random_color) ) {
                $this->colors[$key] = imagecolorallocate($im, $red + $this->random_color[$key], $green + $this->random_color[$key], $blue + $this->random_color[$key]);
            }
            else {
                $this->colors[$key] = imagecolorallocate($im, $this->colors[$key][0], $this->colors[$key][1], $this->colors[$key][2]);
            }
        }

        // Create the rectangle
        imagefilledrectangle($im, 0, 0, $this->img_width, $this->img_height, $this->colors['background']);

        // -----------------------------------
        //  Create the spiral pattern
        // -----------------------------------
        $theta		= 1;
        $thetac		= 7;
        $radius		= 16;
        $circles	= 20;
        $points		= 32;

        for ($i = 0, $cp = ($circles * $points) - 1; $i < $cp; $i++)
        {
            $theta += $thetac;
            $rad = $radius * ($i / $points);
            $x = ($rad * cos($theta)) + $x_axis;
            $y = ($rad * sin($theta)) + $y_axis;
            $theta += $thetac;
            $rad1 = $radius * (($i + 1) / $points);
            $x1 = ($rad1 * cos($theta)) + $x_axis;
            $y1 = ($rad1 * sin($theta)) + $y_axis;
            imageline($im, $x, $y, $x1, $y1,  $this->colors['grid']);
            $theta -= $thetac;
        }

        // -----------------------------------
        //  Write the text
        // -----------------------------------


        ($this->font_size > 30) && $this->font_size = 30;
        $x = mt_rand(1, $this->img_width - ( $length * $this->font_size ));
        $y = $this->font_size + 2;

        for ($i = 0; $i < $length; $i++)
        {
            $y = mt_rand($this->img_height / 2, $this->img_height - 3);
            imagettftext($im, $this->font_size, $angle, $x, $y, $this->colors['text'], $this->font_path, $this->word[$i]);
            $x += $this->font_size;
        }


        // -----------------------------------
        //  Create the border
        // -----------------------------------

        if ($this->use_border === true) {
            imagerectangle($im, 0, 0, $this->img_width-1, $this->img_height-1, $this->colors['border']);
        }


        // -----------------------------------
        //  Generate the image
        // -----------------------------------

        \DeftCMS\Engine::$DT->output->set_content_type('image/jpeg');
        imagejpeg($im);
        imagedestroy($im);

        \DeftCMS\Engine::$DT->session->set_flashdata($this->flash_session_name, $this->word);
        \DeftCMS\Engine::$DT->output->_display();
    }

    /**
     * Проверка капчи
     * Captcha check
     *
     * @param string $value
     * @param string $ip_address
     *
     * @return bool
     */
    public function validate(string $value, string $ip_address = null)
    {
        return $value === \DeftCMS\Engine::$DT->session->flashdata($this->flash_session_name);
    }

    /**
     * Сгенерировать текст для капчи
     * Generate captcha code
     *
     * @return string
     */
    protected function _getWorld()
    {
        $word = '';
        $pool_length = strlen($this->pool);
        $rand_max = $pool_length - 1;

        // PHP7 or a suitable polyfill
        if (function_exists('random_int'))
        {
            try
            {
                for ($i = 0; $i < $this->word_length; $i++)
                {
                    $word .= $this->pool[random_int(0, $rand_max)];
                }
            }
            catch (\Exception $e)
            {
                // This means fallback to the next possible
                // alternative to random_int()
                $word = '';
            }
        }


        if (empty($word))
        {
            // Nobody will have a larger character pool than
            // 256 characters, but let's handle it just in case ...
            //
            // No, I do not care that the fallback to mt_rand() can
            // handle it; if you trigger this, you're very obviously
            // trying to break it. -- Narf
            if( $pool_length > 256 )
            {

                // We'll try using the operating system's PRNG first,
                // which we can access through CI_Security::get_random_bytes()
                $security = get_instance()->security;

                // To avoid numerous get_random_bytes() calls, we'll
                // just try fetching as much bytes as we need at once.
                if (($bytes = $security->get_random_bytes($pool_length)) !== FALSE)
                {
                    $byte_index = $word_index = 0;
                    while ($word_index < $this->word_length)
                    {
                        // Do we have more random data to use?
                        // It could be exhausted by previous iterations
                        // ignoring bytes higher than $rand_max.
                        if ($byte_index === $pool_length)
                        {
                            // No failures should be possible if the
                            // first get_random_bytes() call didn't
                            // return FALSE, but still ...
                            for ($i = 0; $i < 5; $i++)
                            {
                                if (($bytes = $security->get_random_bytes($pool_length)) === FALSE)
                                {
                                    continue;
                                }

                                $byte_index = 0;
                                break;
                            }

                            if ($bytes === FALSE)
                            {
                                // Sadly, this means fallback to mt_rand()
                                $word = '';
                                break;
                            }
                        }

                        list(, $rand_index) = unpack('C', $bytes[$byte_index++]);
                        if ($rand_index > $rand_max)
                        {
                            continue;
                        }

                        $word .= $this->pool[$rand_index];
                        $word_index++;
                    }
                }
            }
        }

        if (empty($word))
        {
            for ($i = 0; $i < $this->word_length; $i++)
            {
                $word .= $this->pool[mt_rand(0, $rand_max)];
            }
        }

        return $word;
    }

    /**
     * Вернуть название обработчика
     * Return handler name
     *
     * @throws \Twig\Error\LoaderError
     * @throws \Twig\Error\RuntimeError
     * @throws \Twig\Error\SyntaxError
     *
     * @return string
     */
    public function getCaptchaTemplate()
    {
        return \DeftCMS\Engine::$DT->template->renderLayer('captcha', [ 'captcha' => ['handler' => $this->getName() ] ], true);
    }

    /**
     * Вернуть название обработчика
     * Return handler name
     *
     * @return string
     */
    public function getName()
    {
        return 'system';
    }
}