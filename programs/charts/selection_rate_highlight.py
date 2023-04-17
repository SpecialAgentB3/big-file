data = [('00', 21, 11, '0.52381'),
('01', 26, 10, '0.38462'),
('02', 18, 12, '0.66667'),
('03', 26, 13, '0.50000'),
('04', 20, 10, '0.50000'),
('05', 20, 13, '0.65000'),
('06', 29, 17, '0.58621'),
('07', 26, 15, '0.57692'),
('08', 23, 12, '0.52174'),
('09', 23, 7, '0.30435'),
('10', 21, 10, '0.47619'),
('11', 29, 12, '0.41379'),
('12', 20, 9, '0.45000'),
('13', 29, 17, '0.58621'),
('14', 21, 12, '0.57143'),
('15', 16, 3, '0.18750'),
('16', 24, 12, '0.50000'),
('17', 19, 9, '0.47368'),
('18', 28, 13, '0.46429'),
('19', 19, 9, '0.47368'),
('20', 23, 9, '0.39130'),
('21', 23, 11, '0.47826'),
('22', 21, 8, '0.38095'),
('23', 15, 10, '0.66667'),
('24', 21, 15, '0.71429'),
('25', 29, 13, '0.44828'),
('26', 22, 9, '0.40909'),
('27', 33, 11, '0.33333'),
('28', 29, 7, '0.24138'),
('29', 25, 12, '0.48000'),
('30', 23, 11, '0.47826'),
('31', 16, 8, '0.50000'),
('32', 17, 8, '0.47059'),
('33', 33, 13, '0.39394'),
('34', 25, 17, '0.68000'),
('35', 18, 5, '0.27778'),
('36', 25, 14, '0.56000'),
('37', 16, 7, '0.43750'),
('38', 23, 11, '0.47826'),
('39', 18, 8, '0.44444'),
('40', 23, 15, '0.65217'),
('41', 20, 12, '0.60000'),
('42', 18, 8, '0.44444'),
('43', 21, 14, '0.66667'),
('44', 28, 16, '0.57143'),
('45', 19, 9, '0.47368'),
('46', 14, 6, '0.42857'),
('47', 24, 9, '0.37500'),
('48', 21, 11, '0.52381'),
('49', 20, 13, '0.65000'),
('50', 26, 8, '0.30769'),
('51', 21, 14, '0.66667'),
('52', 25, 11, '0.44000'),
('53', 23, 11, '0.47826'),
('54', 17, 8, '0.47059'),
('55', 25, 13, '0.52000'),
('56', 15, 5, '0.33333'),
('57', 25, 17, '0.68000'),
('58', 26, 13, '0.50000'),
('59', 24, 10, '0.41667'),
('60', 26, 16, '0.61538'),
('61', 21, 9, '0.42857'),
('62', 17, 10, '0.58824'),
('63', 20, 10, '0.50000'),
('64', 22, 11, '0.50000'),
('65', 22, 13, '0.59091'),
('66', 15, 5, '0.33333'),
('67', 26, 12, '0.46154'),
('68', 20, 10, '0.50000'),
('69', 21, 12, '0.57143'),
('70', 23, 13, '0.56522'),
('71', 21, 11, '0.52381'),
('72', 20, 7, '0.35000'),
('73', 21, 10, '0.47619'),
('74', 24, 9, '0.37500'),
('75', 20, 12, '0.60000'),
('76', 21, 15, '0.71429'),
('77', 25, 14, '0.56000'),
('78', 20, 8, '0.40000'),
('79', 22, 12, '0.54545'),
('80', 19, 11, '0.57895'),
('81', 14, 9, '0.64286'),
('82', 32, 14, '0.43750'),
('83', 20, 7, '0.35000'),
('84', 19, 10, '0.52632'),
('85', 20, 14, '0.70000'),
('86', 26, 13, '0.50000'),
('87', 22, 10, '0.45455'),
('88', 24, 17, '0.70833'),
('89', 26, 12, '0.46154'),
('90', 22, 8, '0.36364'),
('91', 23, 16, '0.69565'),
('92', 26, 15, '0.57692'),
('93', 22, 13, '0.59091'),
('94', 25, 13, '0.52000'),
('95', 27, 13, '0.48148'),
('96', 25, 14, '0.56000'),
('97', 20, 10, '0.50000'),
('98', 25, 9, '0.36000'),
('99', 22, 14, '0.63636')]

import matplotlib.pyplot as plt
from matplotlib.font_manager import FontProperties

labels = [item[0] for item in data]
heights = [float(item[3]) for item in data]

list1 = [1 if (i - 4) % 1 == 0 else 0 for i in range(len(data))]
list2 = [1 if (i) % 5 == 0 else 0 for i in range(len(data))]

def get_zorders(highlight_list, base_zorder=0, highlight_zorder=1):
    return [base_zorder if i == 0 else highlight_zorder for i in highlight_list]

def make_color(color):
    return tuple([(i/255) for i in color])

def get_colors(highlight_list):
    COL1 = make_color((0,0,0))
    COL2 = make_color((104, 80, 191))
    return [COL1 if i == 0 else COL2 for i in highlight_list]

ListNum = 1
highlight_list = list1

# Graph
font = {'family': 'sans-serif', 'weight': 'regular', 'size': 40, 'color': make_color((0,0,0))}
fig, ax = plt.subplots(facecolor=make_color((255, 255, 255)))
ax.set_xlabel('Price Endings', fontsize=46, fontdict=font)
ax.set_ylabel('Selection Rate', fontsize=46, fontdict=font)

# Bars
bars = ax.bar(labels, heights, color=get_colors(highlight_list), width=0.9)

# Ticks
ax.tick_params(axis='both', which='both', colors=make_color((38,38,38)))
tick_font = FontProperties(family='sans-serif', weight='regular', size='20')

ax.set_ylim(0, 0.8)
ax.set_yticks([0, 0.2, 0.4, 0.6, 0.8])

highlighted_labels = [label if highlight == 1 else '' for label, highlight in zip(labels, highlight_list)]
ax.set_xticks(labels)
ax.set_xticklabels(highlighted_labels, fontproperties=tick_font)
ax.set_yticklabels(ax.get_yticks(), fontproperties=tick_font)

# Misc
ax.set_facecolor(make_color((255, 255, 255)))
ax.spines['top'].set_visible(False)
ax.spines['right'].set_visible(False)
for bar, zorder in zip(bars, get_zorders(highlight_list)):
    bar.set_zorder(zorder)


def on_click(event):
    global highlight_list
    global ListNum

    # Rotate highlight_list
    ListNum = (ListNum+1)%2
    highlight_list = list1 if ListNum == 1 else list2

    # Update bar colors and z-orders
    new_colors = get_colors(highlight_list)
    new_zorders = get_zorders(highlight_list)
    for bar, new_color, new_zorder in zip(bars, new_colors, new_zorders):
        bar.set_color(new_color)
        bar.set_edgecolor(new_color)
        bar.set_linewidth(0)
        bar.set_zorder(new_zorder)

    # Update xtick labels
    highlighted_labels = [label if highlight == 1 else '' for label, highlight in zip(labels, highlight_list)]
    ax.set_xticklabels(highlighted_labels)

    plt.draw()


# Connect the click event to the on_click function
fig.canvas.mpl_connect('button_press_event', on_click)

plt.show()
