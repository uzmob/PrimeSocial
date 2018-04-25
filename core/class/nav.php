<?php

/**
 * @package     Prime Social
 * @link        http://primesocial.ru
 * @copyright   Copyright (C) 2016 Prime Social
 * @author      BoB | http://primesocial.ru/about
 */

/**
 * Class Navigator
 */
class Navigator
{
    /**
     * Navigator constructor.
     * @param $all
     * @param $pnumber
     * @param string $query
     */
    public function __construct($all, $pnumber, $query = '')
    {
        $this->all = $all;
        $this->pnumber = $pnumber;
        $this->query = $query;
        $this->p = isset($_GET['p']) ? (int)$_GET['p'] : 1;
        if (isset($_POST['p'])) {
            $this->p = (int)$_POST['p'];
        }


    }

    /**
     * @return float|int
     */
    public function start()
    {
        $this->num_ps = ceil($this->all / $this->pnumber);
        if (isset($_GET['last']))
            $this->p = $this->num_ps;
        $this->start = $this->p * $this->pnumber - $this->pnumber;
        if ($this->p > $this->num_ps || $this->p < 1) {
            $this->p = 1;
            $this->start = 0;
        }
        return $this->start;
    }

    /**
     * @return string
     */
    public function navi()
    {
        global $lng;
        if ($this->num_ps < 2)
            return '';
        $buff = DIV_BLOCK;

        if ($this->p > 1) {
            $n = $this->p;
            $n--;
            $buff .= '&laquo;<a href="' . $_SERVER['SCRIPT_NAME'] . '?p=' . $n . '&amp;' . $this->query . '">' . $lng['Orqaga'] . '</a>';
        }

        if (($this->p > 1) && ($this->p != $this->num_ps)) {
            $buff .= ' | ';
        }

        if ($this->p != $this->num_ps) {
            $p = $this->p;
            $p++;
            $buff .= ' <a href="' . $_SERVER['SCRIPT_NAME'] . '?p=' . $p . '&amp;' . $this->query . '">' . $lng['Oldinga'] . '</a>&raquo;';
        }
        $buff .= '<br/>';
        for ($pr = '', $i = 1; $i <= $this->num_ps; $i++) {
            $buff .=
            $pr = (($i == 1 || $i == $this->num_ps || abs($i - $this->p) < 5) ? ($i == $this->p ? " [$i] " : ' <a href="' . $_SERVER['SCRIPT_NAME'] . '?p=' . $i . '&amp;' . $this->query . '">' . $i . '</a> ') : (($pr == ' .. ' || $pr == '') ? '' : ' .. '));
        }
        if ($this->num_ps > 9) {
            $buff .= '</div>' . DIV_BLOCK . '<form action="' . $_SERVER['SCRIPT_NAME'] . '?' . $this->query . '" method="POST">
                <input type="text" size="3" name="p" value="' . $this->p . '">
                <INPUT type="submit" name="" value="&gt;&gt;"><br></FORM>';
        }

        $buff .= '</div>';
        return $buff . '';
    }
}