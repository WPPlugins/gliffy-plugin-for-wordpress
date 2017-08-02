#!/bin/sh

#Remove any old tests
rm ../test-results/*

#Run the tests
php syntax.php
php TestSuite.php 
