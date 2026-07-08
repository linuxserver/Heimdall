<?php

/*
 * Testing handling of properties set inline.
 */
// phpcs:set TestStandard.SetProperty.PropertyTypeHandling expectsString arbitraryvalue
// phpcs:set TestStandard.SetProperty.PropertyTypeHandling expectsTrimmedString   some value
// phpcs:set TestStandard.SetProperty.PropertyTypeHandling emptyStringBecomesNull

// phpcs:set TestStandard.SetProperty.PropertyTypeHandling expectsIntButAcceptsString 12345
// phpcs:set TestStandard.SetProperty.PropertyTypeHandling expectsFloatButAcceptsString 12.345

// phpcs:set TestStandard.SetProperty.PropertyTypeHandling expectsNull null
// phpcs:set TestStandard.SetProperty.PropertyTypeHandling expectsNullCase NULL

// phpcs:set TestStandard.SetProperty.PropertyTypeHandling expectsBooleanTrue true
// phpcs:set TestStandard.SetProperty.PropertyTypeHandling expectsBooleanTrueCase True
// phpcs:set TestStandard.SetProperty.PropertyTypeHandling expectsBooleanTrueTrimmed    true
// phpcs:set TestStandard.SetProperty.PropertyTypeHandling expectsBooleanFalse false
// phpcs:set TestStandard.SetProperty.PropertyTypeHandling expectsBooleanFalseCase fALSe
// phpcs:set TestStandard.SetProperty.PropertyTypeHandling expectsBooleanFalseTrimmed      false

// phpcs:set TestStandard.SetProperty.PropertyTypeHandling expectsArrayWithOnlyValues[] string, 10, 1.5, , null, true, false
// phpcs:set TestStandard.SetProperty.PropertyTypeHandling expectsArrayWithKeysAndValues[] string=>string,10=>10,float=>1.5,=>,null=>null,true=>true,false=>false
// phpcs:set TestStandard.SetProperty.PropertyTypeHandling expectsEmptyArray[]

// phpcs:set TestStandard.SetProperty.PropertyTypeHandling expectsOldSchoolArrayWithOnlyValues[] string, 10, 1.5, , null, true, false
// phpcs:set TestStandard.SetProperty.PropertyTypeHandling expectsOldSchoolArrayWithKeysAndValues[] string=>string,10=>10,float=>1.5,=>,null=>null,true=>true,false=>false
// phpcs:set TestStandard.SetProperty.PropertyTypeHandling expectsOldSchoolEmptyArray[]

echo 'hello!';
