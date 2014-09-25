#!/bin/bash
# Loops through link files and downloads youtube videos using youtube-dl cli
# An IFTTT Recipe Adds Links to "Youtube_Links" folder based on "Watch_Later" selection. 
# Tyler Smith
# 9/25/14

for file in Youtube_Links/*
do
    # Check if file exists
    test -f "$file" || continue
    # Download Youtube Video. Specified format b/c I wanted to conserve dropbox space. Details here: https://github.com/rg3/youtube-dl
    youtube-dl --no-playlist --format 18 "$(< $file)"
    # Make a new directory to save history of links
    mkdir Youtube_Links_Completed
    # Move link to new folder
    mv $file Youtube_Links_Completed/
done
