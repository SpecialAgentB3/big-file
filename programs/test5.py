import matplotlib.pyplot as plt
import numpy as np
import sympy as smp
from sympy.abc import x, y, e

# sympy code
eq = smp.exp(-((x-5)/4)**2 - ((y-1)/2)**2)


def calculate(x,y):
    return smp.solve(eq,x,y)

plt.style.use('default')

# make data
X, Y = np.meshgrid(np.linspace(-10, 10, 256), np.linspace(-10, 10, 256))
Z = calculate(X,Y)
levels = np.linspace(Z.min(), Z.max(), 30)

# plot
fig, ax = plt.subplots()
ax.contourf(X, Y, Z, levels=levels)


def roll(P,duration):
    i=0
    xDiff = smp.diff(calculate(P[0],P[1]),P[0])
    yDiff = smp.diff(calculate(P[0],P[1]),P[1])
    while i <= duration:
        smp.diff(calculate(x),x)

p = roll((2,3),5)

ax.plot(2, 3, marker="o", markersize=5, markeredgecolor="white", markerfacecolor="red")

plt.show()