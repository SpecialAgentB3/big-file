import numpy as np
import pandas as pd
import matplotlib.pyplot as plt
import matplotlib.animation as animation

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

df['item_1_price_ending'] = (df['item_1_fake_price'] % 1) * 100
df['item_2_price_ending'] = (df['item_2_fake_price'] % 1) * 100

df_long = pd.melt(df, id_vars=['id', 'selected_item'], value_vars=['item_1_price_ending', 'item_2_price_ending'], var_name='item', value_name='price_ending')

def get_selection_rates(df):
    df = df.copy()  # Create a copy of the input DataFrame to avoid the warning
    df.loc[:, 'is_selected'] = df['selected_item'].apply(lambda x: f'item_{x}_price_ending') == df['item']
    df['is_selected'] = df['is_selected'].astype(int)

    grouped = df.groupby(np.round(df['price_ending'])).agg(
        {'is_selected': 'sum', 'id': 'count'}
    )

    selection_rates = (grouped['is_selected'] / grouped['id']).fillna(0)

    return selection_rates


df_list = [df_long[df_long['id'] <= i] for i in range(1, len(df) + 1)]

fig, ax = plt.subplots()
price_endings = list(range(0, 100))
bar_container = ax.bar(price_endings, np.zeros(len(price_endings)))

ax.set_title('Price Ending Selection Rates Over Time')
ax.set_xlabel('Price Ending')
ax.set_ylabel('Selection Rate')
ax.set_ylim([0, 1])

def update_bar_heights(selection_rates):
    for idx, bar in enumerate(bar_container):
        bar.set_height(selection_rates.get(idx, 0))

def animate(i):
    df_filtered = df_list[i]
    selection_rates = get_selection_rates(df_filtered)
    update_bar_heights(selection_rates)

ani = animation.FuncAnimation(fig, animate, frames=len(df_list), interval=50, repeat=False)

plt.show()
