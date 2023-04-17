from PIL import Image, ImageDraw
import pygame

img = Image.new("RGB", (1920, 1080), 'white')

zoom = 50

# Draws the grid
i = 1
while i < img.width/zoom:
    ImageDraw.Draw(img).line(((i*zoom,0),(i*zoom,img.height)),fill="black")
    i = i + 1

i = 1
while i < img.height/zoom:
    ImageDraw.Draw(img).line(((0,i*zoom),(img.width,i*zoom)),fill="black")
    i = i + 1



img.show()
