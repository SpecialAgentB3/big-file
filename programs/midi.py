import mido
import pyautogui
import math

inport = mido.open_input()

key = 60
def major(x):
    return math.floor(3/5 * x)

def minor(x):
    return math.floor(2/3 * x)
    #https://www.desmos.com/calculator/gyacnpfy1o


for Note in inport:
    if Note.type == 'note_on':
        move = minor(Note.note - key) * 72 + 745
        pyautogui.moveTo(move, 475)
        pyautogui.press('1')
        print(minor(Note.note - key))