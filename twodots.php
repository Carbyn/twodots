<?php
class Twodots {

    private $board; // N x N
    private $board_arr; // N^2 * 1

    private $rows;
    private $columns;

    public function init($row = 6, $column = 6) {
        $this->rows = $row;
        $this->columns = $column;
        $this->board = [];
        for ($x = 0; $x < $row; $x++) {
            for ($y = 0; $y < $column; $y++) {
                $this->board[$x][$y] = 0;
                $this->board_arr[$x.'_'.$y] = 0;
            }
        }
    }

    public function draw() {
        $color = 0;
        while (true) {
            if (empty($this->board_arr)) {
                break;
            }
            $color++;
            list($x, $y) = $this->getPointRandomly();
            echo "start point, $x, $y, $color\n";
            $length = rand(8, 15);
            if ($this->go($x, $y, $color, $length) == 0) {
                $color--;
            }
        }
    }

    public function output() {
        echo "board:\n";
        for ($x = 0; $x < $this->rows; $x++) {
            echo implode(" ", $this->board[$x])."\n";
        }
    }

    private function getPointRandomly() {
        $point = array_rand($this->board_arr);
        return explode('_', $point);
    }

    private function takePoint($x, $y, $color) {
        echo "takePoint $x, $y, $color\n";
        $point = $x.'_'.$y;
        unset($this->board_arr[$point]);
        $this->board[$x][$y] = $color;
        $this->output();
    }

    private function untakePoint($x, $y) {
        echo "untakePoint $x, $y\n";
        $point = $x.'_'.$y;
        $this->board_arr[$point] = 0;
        $this->board[$x][$y] = 0;
    }

    private function isPointTaken($x, $y) {
        return $this->board[$x][$y] != 0;
    }

    private function go($x, $y, $color, $length) {
        echo "go color=$color, len=$length\n";
        $record_points = [];
        $true_length = 0;
        while ($length-- > 0) {
            $record_points[] = [$x, $y];
            $this->takePoint($x, $y, $color);
            $true_length++;
            $alone_points = $this->isSomeoneAlone();
            if ($alone_points) {
                foreach($alone_points as $p) {
                    if ($this->canTakePoint($x, $y, $p[0], $p[1])) {
                        $record_points[] = [$p[0], $p[1]];
                        $this->takePoint($p[0], $p[1], $color);
                        $true_length++;
                    } else {
                        foreach($record_points as $p) {
                            $this->untakePoint($p[0], $p[1]);
                            return 0;
                        }
                    }
                }
                return $true_length;
            }

            $point = $this->getNextPoint($x, $y);
            if ($point == false) {
                echo "next point false\n";
                break;
            }
            $x = $point[0];
            $y = $point[1];
            echo "next point $x, $y\n";
        }
        return $true_length;
    }

    private function canTakePoint($x, $y, $xx, $yy) {
        $points = $this->getNextPoint($x, $y, true);
        if (empty($points)) {
            return false;
        }
        foreach($points as $p) {
            if ($p[0] == $xx && $p[1] == $yy) {
                return true;
            }
        }
        return false;
    }

    private function getNextPoint($x, $y, $return_all = false) {
        $points = [];
        if ($x > 0 && !$this->isPointTaken($x-1, $y)) {
            $points[] = [$x-1, $y];
        }
        if ($y < $this->columns - 1 && !$this->isPointTaken($x, $y+1)) {
            $points[] = [$x, $y+1];
        }
        if ($x < $this->rows - 1 && !$this->isPointTaken($x+1, $y)) {
            $points[] = [$x+1, $y];
        }
        if ($y > 0 && !$this->isPointTaken($x, $y-1)) {
            $points[] = [$x, $y-1];
        }
        if (empty($points)) {
            return false;
        }
        if ($return_all) {
            return $points;
        } else {
            return $points[array_rand($points)];
        }
    }

    private function isSomeoneAlone() {
        $someone = [];
        for ($x = 0; $x < $this->rows; $x++) {
            for ($y = 0; $y < $this->columns; $y++) {
                if (!$this->isPointTaken($x, $y) &&
                    $this->getNextPoint($x, $y) == false) {
                    echo "$x, $y is alone\n";
                    $someone[] = [$x, $y];
                }
            }
        }
        if (empty($someone)) {
            return false;
        }
        $this->output();
        return $someone;
    }

}

$twodots = new Twodots();
$twodots->init();
$twodots->output();
$twodots->draw();
$twodots->output();
