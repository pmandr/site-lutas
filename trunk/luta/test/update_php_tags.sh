#!/bin/bash

sed -i 's/<?=/<?php echo /g' $1
sed -i 's/<?\([^p]\|$\)/<?php\1/g' $1

