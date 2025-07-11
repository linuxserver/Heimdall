<?php

function nestingOne()
{
    if ($condition) {
        echo 'hi';
    }
}

function nestingFive()
{
    if ($condition) {
        echo 'hi';
        switch ($condition)
        {
            case '1':
                if ($condition === '1') {
                    if ($cond) {
                        echo 'hi';
                    }
                }
            break;
        }
    }
}

function nestingSix()
{
    if ($condition) {
    } else {
        switch ($condition) {
            case '1':
                if ($condition === '1') {
                } elseif ($condition === '2') {
                    do {
                        foreach ($conds as $cond) {
                            echo 'hi';
                        }
                    } while ($cond > 5);
                }
            break;
        }
    }
}

function nestingTen()
{
    if ($condition) {
        echo 'hi';
        switch ($condition)
        {
            case '1':
                if ($condition === '1') {
                    if ($cond) {
                        switch ($cond) {
                            case '1':
                                if ($cond === '1') {
                                    foreach ($conds as $cond) {
                                        if ($cond === 'hi') {
                                            echo 'hi';
                                        }
                                    }
                                }
                            break;
                        }
                    }
                }
            break;
        }
    }
}

function nestingEleven()
{
    if ($condition) {
        echo 'hi';
        switch ($condition)
        {
            case '1':
                if ($condition === '1') {
                    if ($cond) {
                        try {
                            if ( $cond === '1' ) {
                                for ( $i = 0; $i < 10; $i ++ ) {
                                    while ($i < 5) {
                                        if ( $cond === 'hi' ) {
                                            match ( $cond ) {
                                                'hi' => 'something',
                                            };
                                        }
                                    }
                                }
                            }
                        } catch (Exception $e) {}
                    }
                }
            break;
        }
    }
}

abstract class AbstractClass {
    abstract public function sniffShouldIgnoreAbstractMethods();
}

interface MyInterface {
    public function sniffShouldIgnoreInterfaceMethods();
}
