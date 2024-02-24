# Cloud Compress
Cloud Compress converts files into tiny versions of themselves by hashing binary blobs of the files into hashes. With the hashes being much smaller than the binary blobs, we achieve levels of file compression that surpass most common compression algorithms by far. 

[View Demo](https://akaninyene.com/projects/cloud-compress/)

<img src="assets/screenshot.jpg" height="300px">

## Usage

**Dependencies**: PHP, Sqlite3

Create the database for the hash_table in the project directory.
```
% sqlite3 database.sqlite

sqlite> CREATE TABLE hash_table(
hash_table_hash TEXT PRIMARY KEY,
hash_table_val  TEXT
);
```
From the project repository run the following command, then navigate to https://localhost:8000 from a web browser.
```
php -S localhost:8000
```

## File Compression
Compression is achieved by hashing 2^16 or 65,536 byte binary chunks of files into 4 byte crc32 hashes. This produces a compression rate of 1/16,384. The compressed file is produced from the hashes of the binary chunks of the original file.

<img src="assets/hash_table.jpg">

## File Decompression
This process can be reversed to decompress files. The compressed file is decompressed by looking up the file content for each hash value, then converting the hashed content to the original file's content.

<img src="assets/hash_table2.jpg">

## Limitations

Anybody is free to fork this repository and improve on Cloud Compress. Some limitations and potential improvements are:
- The file size limit is 2.1 MB due to the PHP web server limitations.
- Add a column to handle logistics for collisions in hash table.
- Add encryption or a type of obfuscation so that individual binary chunks are not easily reversible in database.