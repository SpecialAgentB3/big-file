from ast import AsyncFunctionDef
import os
from PIL import Image
import numpy as np
import matplotlib.pyplot as plt


# with Image.open("C:/Users/Ben/Desktop/Everything/pictures/Screenshot_1575.png") as im:
#     im.rotate(45).show()


# # File reading: 

# file_name = "C:/Users/Ben/Desktop/Everything/programs/readfile.txt"
# file_exists = os.path.exists(file_name)
# if file_exists:
#     print('Hello ' + file_name  )
#     f = open(file_name, "r")
#     print(f.read())
#     f.close()
# else:    
#     print('COULD NOT FIND ' + file_name  )

#otherwas to read text from
#with open('C:\Users\Ben\Desktop\Everything\programs\readfile.txt') as f:
#    lines = f.readlines()


#Directory looping:
# https://www.geeksforgeeks.org/how-to-iterate-over-files-in-directory-using-python/
 
# iterate over files in
# that directory

#for filename in os.listdir(directory):
#    print(filename)
#exit("bye")

# directory = 'C:/Users/Ben/AppData/Roaming/.minecraft/saves/Test World 1/datapacks/Raycasting II/data/ray/functions'
#directory = r'C:\Users\Ben\Desktop\Everything\programs\visualize\function1'
redirect = 'function '

# "con" for "connections" (to other files/functions)
class Connections:


    # Return list containing the text succeeding "redirect" it in the current file
    def file(file):
        try: 
            with open(file, 'r') as f:
                return([line.split(redirect)[1].rstrip() for line in f if redirect in line and 'schedule' not in line]) 
        except PermissionError:
            print("That's a folder dumbass")


    # Goes through all folders and runs Connections.file() for each one.
    def dir(List, directory):
        if os.path.isdir(directory):
            for filename in os.listdir(directory):
                Connections.dir(List, os.path.join(directory, filename))
        if os.path.isfile(directory):
            List.append(Connections.file(directory))


    # Necessary to set L1 to [] in a seperate function because Connections.dir iterates over itself
    def list(directory):
        L1 = []
        Connections.dir(L1, os.path.join(directory))
        return(L1)


class Display:

    def createImage(directory):
        img = Image.new("RGB", (1920, 1080), 'Teal')

        Files = Connections.list(directory)

        print(Files)






        img.show()
    
    def calculate(x):
        return np.exp(
            -((x[0]-5)/3.3)**2 - ((x[1]-1)/2)**2
        )

    def roll():
        i = 1
        while i

plt.style.use('_mpl-gallery-nogrid')


print(Display.calculate((4,2)))
#Display.createImage(r'C:\Users\Ben\AppData\Roaming\.minecraft\saves\Test World 1\datapacks\Raycasting II\data\ray\functions\calculate.mcfunction')
#print(Connections.file(r'C:\Users\Ben\AppData\Roaming\.minecraft\saves\Test World 1\datapacks\Raycasting II\data\ray\functions\calculate.mcfunction'))