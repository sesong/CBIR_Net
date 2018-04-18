import webbrowser as web
import os
import shutil
import time
import random
import numpy as np

#이미지가 다운받아지는 주소
similar_path = 'C:/xampp/htdocs/proposed_similar/'

#cbir php 코드가 있는 주소
cbir_path = 'C:/xampp/htdocs/'

#검색할 이미지 주소
q_image_list_dir = 'E:/dataset/cifar-100-python/pixel_data/'

sim_down_dir = 'E:/dataset/cifar-100-python/sim/'

#gist_path = 'C:/Users/sesong/Desktop/gistdescriptor/'   
#gist_filename = 'GistOutput.txt'

#검색할 이미지의 인덱스 리스트

#검색할 이미지의 이미지 이름 리스트
q_imageName_list = os.listdir(q_image_list_dir)


#랜덤한 이름을 읽어 올 파일의 주소
randomNamePath = os.path.join(cbir_path, "targetName.txt")


imageIdx = 16545

def cbir():
    global imageIdx
    
    #기존 다운로드 이미지 삭제
    for sim_img in os.listdir(similar_path):
        os.remove(os.path.join(similar_path, sim_img)) 
    
    print('Open Chrome Explorer')
    url = 'http://192.168.0.26/proposed.php'
    web.open_new_tab(url)
    print('Downloading...')
    start_time = time.time()
    while True:
        num_d_img = len(os.listdir(similar_path))     
        
        #다운로드 안됨 에러
        if num_d_img == 0:
            if time.time()-start_time > 20:
                print("!!!!!!Download Error!!!!!!")
                break
        
        #폴더에 사진 남아있음 에러
        elif time.time()-start_time > 30:
            d_imgList = os.listdir(similar_path)

            #남은 사진 삭제
            for d_imgName in d_imgList:

                d_imgDir = os.path.join(similar_path,d_imgName)
                
                os.remove(d_imgDir)

            print("!!!!!!Remain Image Error!!!!!!")
            break

        #다섯장이 채워지면 새 폴더를 만들어 저장하고 break
        elif num_d_img >= 5:
            time.sleep(3)
            print('Download Finish')
            dirname = sim_down_dir + str(imageIdx)
            if not os.path.isdir(dirname):
                os.mkdir(dirname)
            else:
                print("!!!!!!There is alreay folder!!!!!!")
                break

            d_imgList = os.listdir(similar_path)

            for d_imgName in d_imgList:

                d_imgDir = os.path.join(similar_path,d_imgName)
                shutil.copy2(d_imgDir, dirname)
                
                os.remove(d_imgDir)    
            break

        

    if imageIdx % 20 == 0:
        time.sleep(10)
        os.system('taskkill /F /IM chrome.exe')
        print('Kill Chrome Explorer')



if __name__ == "__main__":
    top1 = 0.0
    top5 = 0.0
    top1_accuracy = 0.0
    top5_accuracy = 0.0
##################################반복
    print("시작")
    
    

    while True:

        q_imageName = q_imageName_list[imageIdx]
        print("image namae: %s" % q_imageName)                      
        print("image index: %d" % imageIdx)     
        #최종 이미지의 주소
        q_img_path = os.path.join(q_image_list_dir, q_imageName)
                
############## 이전 이미지 삭제

        #이전 이미지 이름을 읽어옴
        with open(randomNamePath, 'r') as f:
            num = f.readline()            
        #이전에 있던 이미지 지우기(저장된 랜덤 변수 불러옴)
        name_deleteFile = cbir_path + 'target_' + num + '.jpg'
        print(name_deleteFile)
        os.remove(name_deleteFile)            
        

############## 새로운 이미지 서버폴더에 만들기

        #새로운 랜덤 변수 만들기
        ranValue = random.randint(1,1000000)
        #새로운 랜덤 변수 저장하기
        with open(randomNamePath, 'w') as f:
            f.write(str(ranValue))
        newFilePath = cbir_path + 'target_' + str(ranValue) + '.jpg'
        #이미지 복사
        shutil.copy2(q_img_path, newFilePath)

############### #cbir로 이미지 다운        
        cbir()              
        imageIdx += 1
      