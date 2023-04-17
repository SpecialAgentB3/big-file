import matplotlib.pyplot as plt
import numpy as np
import sympy as smp
from sympy.abc import x, y


eq = (-((x-5)/4)**2 - ((y-1)/2)**2)

xDiff = smp.diff(eq,x)
yDiff = smp.diff(eq,y)

def calculate(x,y):
    return smp.evalf(sums={x:x,y:y})

print(calculate(3,5))