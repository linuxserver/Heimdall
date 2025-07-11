<?php

function myGenerator() {
    /* testYieldFromHasSingleSpace */
    yield from gen2();

    /* testYieldFromHasMultiSpace */
    yield     from gen2();

    /* testYieldFromHasTabs */
    yield		from gen2();

    /* testYieldFromMixedTabsSpaces */
    Yield	   	 	From gen2();
}
