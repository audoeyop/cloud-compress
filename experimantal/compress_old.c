#include <stdio.h>
#include <string.h>
#include <math.h>

int main ()
{
  FILE * pFile, * pFileW, * pFileC;
  int c;
  int buffer_size = 16;
  int n_buffers = 0;

  pFile=fopen ("ls","r");
  pFileW=fopen ("ls2","w");
  pFileC=fopen ("ls3","w");

  fseek(pFile, 0L, SEEK_END);
  int size = ftell(pFile);
  fseek(pFile, 0L, SEEK_SET);

  int buffer_buf_len = (int) size/buffer_size;

  int buffer[buffer_buf_len+1][buffer_size];

  if (pFileW==NULL) perror ("Error opening file");

  if (pFile==NULL) perror ("Error opening file");
  else
  {
    do {
      //printf("0x");

      //clear array
      //memset(buffer,0x0,sizeof(int));

      for(int i=0; i < buffer_size; i++){
        if(c != EOF){
          c = fgetc (pFile);
          buffer[n_buffers][i] = c;
          //printf("%x", buffer[n_buffers][i]);
        }
      }
      n_buffers++; 
      //printf("\n");
    } while (c != EOF);

    int compression_buffer[buffer_size];

    for(int i=0; i<n_buffers;i++){
      printf("0x");

      memset(compression_buffer,0x0,buffer_size * sizeof(int));
      int compression_num = 0;
      
      for(int j=0; j<buffer_size; j++){
        if(buffer[i][j] != 0xffffffff && buffer[i][j] != 0x7fff){
          compression_num += buffer[i][j] * pow(256, (buffer_size - 1 -j));
          printf(" %02x", buffer[i][j]);
          fputc (buffer[i][j],pFileW);
        }
      }

      //int tmp_compression_num = 0;
      for(int k=0; k<(buffer_size-1); k++){
        //tmp_compression_num = compression_num >> (buffer_size - 1 - k);
        //if(tmp_compression_num == 0){
        //  fputc(0x00, pFileC);
        //}
        //else
        //  break;
      }
      printf(" - cnum: %d (%16X)\n",compression_num,compression_num);

      fputc(compression_num, pFileC);
    }
    fclose (pFile);
    fclose (pFileW);
    fclose (pFileC);
  }
  
FILE * pFile2;
pFile2=fopen ("test","w");
fputc (0x00,pFile2);  
fclose (pFile2);

  return 0;
}
