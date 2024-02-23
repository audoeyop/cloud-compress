#include <stdio.h>
#include <string.h>
#include <math.h>

int main ()
{
  FILE * pFile, * pFileW, * pFileC;
  int c;
  int md5_bytes = 32;
  int buffer_size = 128;
  int n_buffers = 0;

  pFile=fopen ("ls","r");
  pFileW=fopen ("ls2","w");
  pFileC=fopen ("ls3","w");

  int size = ftell(pFile);

  fseek(pFile, 0L, SEEK_END);
  fseek(pFile, 0L, SEEK_SET);
  
  int buffer_len = md5_bytes * buffer_size;

  int buffer_buf_len = (int) size/buffer_size; //delete?

  int buffer[buffer_buf_len+1][buffer_size];
  char str[buffer_len];

  if (pFileW==NULL) perror ("Error opening file");
  if (pFileC==NULL) perror ("Error opening file");
  
  if (pFile==NULL) perror ("Error opening file");
  else
  {
    while( fgets (str, buffer_len, pFile)!=NULL ) {
      printf("%d\n",str);
    }
    fclose (pFile);
    fclose (pFileW);
    fclose (pFileC);
  }
  
//FILE * pFile2;
//pFile2=fopen ("test","w");
//fputc (0x00,pFile2);  
//fclose (pFile2);

  return 0;
}
