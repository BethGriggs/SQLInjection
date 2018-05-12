# Challenge Brief

The challenge interface consists simply of one input field. The aim of the challenge is to successfully inject an SQL statement into the ‘Username’ input field that will return the list of all users. The challenge is focused around a particular edge case that is specific to the character set used. The character set used in the challenge is ‘GBK’.

It is worth noting that the query is implemented using a PHP PDO prepared statement. Therefore, the SQL injection attempts must be tailored to bypass this implementation method.

# Challenge Implementation

The challenge implementation is based on an example described by Shiflett (“Addslashes() versus mysql_real_escape_string()”, 2006). The challenge demonstrates a vulnerability that only occurs when an SQL server is using particular character sets.  The use of the character set ‘GBK’ in the challenge means that it is possible for an attacker to inject a PHP PDO prepared statement. The query response of each SQL injection attempt is output to the interface to demonstrate the vulnerability.

The challenge was implemented with the use of XAMPP with Apache v2.4.9, PHP v5.5.11 and MySQL v5.6.16.  This bypass has been blocked as of MySQL v5.7.6. However, MySQL v5.7.6 was only released in September 2015 (“MySQL :: MySQL 5.7 Release notes”), so it is likely that some systems are still using the older versions of MySQL and, therefore, are still susceptible to this vulnerability.

# Explanation of Exploit

The aim of the challenge in Task 2 is to successfully inject an SQL statement into a PDO prepared statement. PDO prepared statements are typically resilient against SQL injection attacks because the SQL query and parameter values are sent to the server separately. When using PDO prepared statements it is not always considered necessary to escape special characters within input values, because the input values will be sent to the server separately. The use of PDO prepared statements is generally considered as adequate protection from SQL injection attacks. However, by default PDO will emulate prepared statements when interacting with a MySQL database. PDO defaults to emulated prepared statements when interacting with a MySQL database because the PDO emulation can perform the queries significantly faster than MySQL’s native prepared statement implementation.

The vulnerability in the challenge exists because of PDO’s prepared statement emulation. During the emulation, PHP internally builds the query by replacing the placeholders with the corresponding values. A PDO emulated prepared statement does not send the SQL query and data to the server separately, which means that the query is still vulnerable to SQL injection attacks. However, to reduce the likelihood of an attacker injecting through the prepared statement emulation, during the emulation the ‘mysql_real_escape_string’ function is applied to each input value to escape special characters (“PHP: PDO::prepare”, n.d.).  

The PHP function ‘mysql_real_escape_string’ is applied to input values escape the single quote, double quote, backslash and ‘NULL’ byte characters. Any single or double quote characters within an input should always be escaped because these characters are often used inject SQL queries. However, it is actually possible to bypass the ‘mysql_real_escape_string’ function when an SQL server is using particular character sets.

The ‘mysql_real_escape_string’ function should perform escaping methods based on the current character set. The default character set is ‘latin1’. However, in the challenge, the server is set to use the ‘GBK’ character set through a query. The fact the character set is defined through a query means that the client does not know that the character set has changed. Therefore, the ‘mysql_real_escape_string’ function will perform escaping according to the default ‘latin1’ character set despite the SQL server being set to use the ‘GBK’ character set.  The character set mismatch between the client and server is what enables the ‘mysql_real_escape_string’ function to be bypassed.

The ‘mysql_real_escape_string’ function can be bypassed by tricking the function into creating a valid multibyte character instead of escaping a single quote (Shiflett, 2006). For example, in the ‘GBK’ character set the hexadecimal value ‘0xBF27’ is not a valid multibyte character. However, in the ‘latin1’ character set the hexadecimal value ‘0xBF27’ represents the string ‘¿’’. Applying the ‘mysql_real_escape_string’ function to this string will add a backslash before the single quote, resulting in the string ‘¿\’’. The string ‘¿\’’ has a hexadecimal value of ‘0xBF5C27’, however in the ‘GBK’ character set this hexadecimal value represents the string ‘뽜’’. As a result, the server will interpret the hexadecimal value ‘0xBF5C27’ as the string ‘뽜’’. Therefore, inputting the string ‘¿’’ will successfully inject a single quote, despite the use of the ‘mysql_real_escape_string’ function. Consequently, it is possible to use this vulnerability to inject a single quote through a PDO emulated prepared statement.

This bypass method is not restricted to situations where the server is using the ‘GBK’ character set because the bypass occurs at hexadecimal level. It is possible to bypass the ‘mysql_real_escape_string’ function when the SQL server is using any character set encoding where there exists a valid multibyte character with a hexadecimal value ending in ‘0x5C’ (Epadillas, 2012).  Other character sets which contain a valid multibyte character with a hexadecimal value ending in ‘0x5C’ include the ‘SJIS’ and ‘Big5’ character sets.

# Solution

To complete the challenge the value ‘¿' OR 1=1 ##’ can be injected

The ‘¿'’ part of the string enables a single quote to be successfully injected into the PDO prepared statement.  The ‘OR 1=1’ part of the string means that the SQL statement will always evaluate to ‘true’ and, therefore, return all users from the username table. And finally, the ‘##’ part of the string comments out the ‘Limit 1’ part of the SQL statement, meaning more than one row can be returned from the query.
