import pandas as pd
import matplotlib.pyplot as plt
import numpy as np
from matplotlib.animation import FuncAnimation
from matplotlib.font_manager import FontProperties
from matplotlib.ticker import FixedLocator


plt.switch_backend('agg')

column_names = [
    "id",
    "timestamp",
    "item1_id",
    "item_1_actual_price",
    "item_1_fake_price",
    "item2_id",
    "item_2_actual_price",
    "item_2_fake_price",
    "selected_item",
    "response_time",
    "ip_address",
    "uuid"
]

df = pd.read_csv(r'C:\Users\Ben\Desktop\Everything\programs\charts\responses.csv', header=None, names=column_names)

item_1_price_ending = df['item_1_fake_price'] % 1
item_2_price_ending = df['item_2_fake_price'] % 1

def make_color(color):
    return tuple([(i/255) for i in color])

def fade_color(start_color, end_color, val, next_val, max_diff):
    diff = abs(val - next_val)
    factor = diff / max_diff
    return tuple([start_color[i] + (end_color[i] - start_color[i]) * factor for i in range(len(start_color))])

COL1 = make_color((150, 216, 232))
COL2 = make_color((5, 62, 76))


def update(num, counter_selected, counter_ignored):
    plt.cla()

    selected_item, item1_id, item2_id, item_1_fake_price, item_2_fake_price = df.loc[num, ['selected_item', 'item1_id', 'item2_id', 'item_1_fake_price', 'item_2_fake_price']]

    if selected_item == item1_id:
        selected = int(round((item_1_fake_price % 1) * 100))
        ignored = int(round((item_2_fake_price % 1) * 100))
    else:
        selected = int(round((item_2_fake_price % 1) * 100))
        ignored = int(round((item_1_fake_price % 1) * 100))

    counter_selected[selected] += 1
    counter_ignored[ignored] += 1

    # Using np.divide to avoid division by zero
    selection_rates = np.divide(counter_selected, counter_selected + counter_ignored, where=(counter_selected + counter_ignored) != 0)

    # Update the bar colors based on selection_rates
    bar_colors = get_bar_colors(selection_rates)

    font = {'family': 'sans-serif', 'weight': 'regular', 'size': 40, 'color': make_color((0, 0, 0))}
    tick_font = FontProperties(family='sans-serif', weight='regular', size='10')
    ax.bar(np.arange(100), selection_rates, color=bar_colors, width=0.9)
    ax.set_xlabel('Price Ending', fontsize=15, fontdict=font)
    ax.set_ylabel('Selection Rate', fontsize=15, fontdict=font)
    ax.tick_params(axis='both', which='both', colors=make_color((38, 38, 38)))
    ax.set_ylim(0, 1)
    ax.set_xticks(range(100))
    ax.set_xticklabels([f'.{label:02}' if i % 10 == 0 else '' for i, label in enumerate(range(100))], fontproperties=tick_font)
    yticks = ax.get_yticks()
    ax.yaxis.set_major_locator(FixedLocator(yticks))
    ax.set_yticklabels([f'{y:.1f}' for y in yticks], fontproperties=tick_font)


    ax.set_facecolor(make_color((255, 255, 255)))
    ax.spines['top'].set_visible(False)
    ax.spines['right'].set_visible(False)

    # response_num = num + 1  # Adjust for zero-based indexing
    plt.title(f'Selection Rate after {num+1} responses')


def get_bar_colors(selection_rates):
    heights = selection_rates
    diffs = [abs(heights[i] - heights[i+1]) for i in range(len(heights)-1)]
    max_diff = max(diffs)

    bar_colors = []
    for i in range(len(heights)-1):
        color = fade_color(COL1, COL2, heights[i], heights[i+1], max_diff)
        bar_colors.append(color)

    # Calculate the color for the last bar based on the difference between the first and the last bar
    last_color = fade_color(COL1, COL2, heights[-1], heights[0], max_diff)
    bar_colors.append(last_color)
    
    return bar_colors

counter_selected = np.zeros(100)
counter_ignored = np.zeros(100)
num_responses = len(df)
fig, ax = plt.subplots(facecolor=make_color((255, 255, 255)))
fig.subplots_adjust(left=0.10, bottom=0.10)
ani = FuncAnimation(fig, update, frames=range(num_responses), fargs=(counter_selected, counter_ignored), repeat=False, blit=False, interval=100)
# ani.save('animation.mp4', writer='ffmpeg', fps=30)