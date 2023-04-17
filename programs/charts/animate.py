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

df['item_1_price_ending'] = np.round((df['item_1_fake_price'] % 1) * 100).astype(int)
df['item_2_price_ending'] = np.round((df['item_2_fake_price'] % 1) * 100).astype(int)

def get_selection_rates(df, max_id):
    df_filtered = df[df['id'] <= max_id].copy()

    selected_counts = df_filtered[df_filtered['selected_item'].isin([1, 2])].apply(
        lambda x: f'item_{x["selected_item"]}_price_ending', axis=1
    )
    
    selected_counts = pd.concat([selected_counts, df_filtered.loc[selected_counts.index, 'selected_item']], axis=1)
    selected_counts.columns = ['item', 'selected_item']
    
    selected_counts = selected_counts.groupby(['item', 'selected_item']).size().reset_index(name='counts')
    selected_counts.set_index('item', inplace=True)

    total_presented_1 = df_filtered.groupby(np.round(df_filtered['item_1_price_ending'])).size()
    total_presented_2 = df_filtered.groupby(np.round(df_filtered['item_2_price_ending'])).size()
    total_presented = total_presented_1.add(total_presented_2, fill_value=0)

    selection_rates = selected_counts['counts'].divide(total_presented, fill_value=0)
    return selection_rates




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
    max_id = i + 1
    selection_rates = get_selection_rates(df, max_id)
    update_bar_heights(selection_rates)

ani = animation.FuncAnimation(fig, animate, frames=len(df), interval=100, repeat=False)

plt.show()
