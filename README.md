# Cloud Compress
Cloud Compress converts files into tiny versions of themselves by hashing binary blobs of the files into hashes. With the hashes being much smaller than the binary blobs, we achieve levels of file compression that surpass most common compression algorithms by far. 

[View Demo](https://akaninyene.com/projects/cloud-compress/)

<img src="assets/screenshot.jpg" height="300px">


Compression is achieved by hashing 2^16 or 65,536 byte binary chunks of files into 4 byte crc32 hashes. This produces a compression rate of 1/16,384. The compressed file is produced from the hashes of the binary chunks of the original file.

<img src="assets/hash_table.jpg">

