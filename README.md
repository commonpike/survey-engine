# surveyengine

Install this on a webserver, create a database
and import survey.sql. That gives you
a survey with 

*8 fields*, 
which can have a value
like 'name':'john'

*16 categories*, 
which can have predefined options within the categories,
like 'age' : '16-32'

*32 statements*,
which can each have one of a number of predefined 'positions'
like 'I like strawberry' : 'yes', 'I like applepie' : 'no'

==setup==
Edit survey.json.dist to fill out your 
fields, categories and statements.
the 'column' names should match the 
database column names. when done,
save it as survey.json

Quickly look at config.php.dist to set
an admin password and the database
credentials. when done, move it 
to config.php

==test it==
Now open survey.php in your browser. it
prints html results by default, but it
can export results in csv or json,
it can display a form to submit
new entries or clear the database
using the admin password.

You can submit new entries using GET 
requests and get a result in a variety
of formats, including json.

==go live==
Once you are ready to go live, turn
$config->testing to false in config.php.





