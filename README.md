# openclub-csv

A plugin which adds a custom post type called CSV, allows rules to be applied to the columns and has functionality for display and CLI processing.

The main use cases for this were:

1. Adding content to a site from excel sheets (with export to CSV).
2. Being able to further process said CSV files.

Example CSV content stored in post content:

```
Day,Date,Event,Time,Team,Note,IsJunior
Sun,3/24/18,Winter Fun Sailing,1100,,,
Sat,3/24/18,New Members Induction,1100,,,
Sun,3/25/18,BST STARTS - CLOCKS FORWARD 1 HOUR,,,,
Sun,3/25/18,Boat move and beach clean,1100,,,
Sat,3/31/18,The Opener,1400,3,,
Sun,4/1/18,Spring Series 1 of 10 - Spring Berthing starts,1100,4,,
```

Example CSV field rules, stored in a meta field:

```
[Day]
type = string
options = Sat,Sun,Thu,Tue,Mon,Fri


[Date]
type = date
input_format = m/d/y
output_format = d/m/Y

[Event]
type = string
max-length = 60
required = true

[Time]
type = string
options = 1100,1030,1830,1900,1400,1800,0830,TBA,0930

[Team]
type = string
options = A,B,C,D,E,F,G,H,J,1,2,3,4,5,6,7,8,9
display = false

[Note]
type = string
max-length = 45
display = false

[IsJunior]
type = int
options = 1
display = false
```

The rules enforce rules on the data and provide optional feedback on the web interface.


# Examples in production use:

http://www.swanagesailingclub.org.uk/social-events/
http://www.swanagesailingclub.org.uk/sailing-programme/2018/
http://www.swanagesailingclub.org.uk/ (next sailing events, pulling data from the sailing programme (above) ).

# Can I use it?

Not yet, the APIs aren't fixed yet.
