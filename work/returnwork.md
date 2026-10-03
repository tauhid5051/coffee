When editing an existing sale:

If an existing item's quantity is reduced:

The difference becomes a return quantity.

Example: sold 5, edit to 3 → return 2.

If an existing item is completely removed from the sale:

The entire original quantity becomes a return.

Example: sold 5, remove item → return 5.

If a new item is added during sale editing:

It is not a return.

The return should be recorded in your existing:

returns

return_items

The return should have the appropriate:

product

quantity

warehouse

price/amount

original sale relationship/reference

Your existing Return Report should then show these returns and their amounts.

Important
I also understand that you do not want a separate manual return process for this workflow.

The return is automatically created/updated as part of Sale Edit.

And because this is an existing project with stock/costing logic, we should be particularly careful not to accidentally:

duplicate stock

return an item twice

change historical sale totals incorrectly

break costing/FIFO

create duplicate return records

affect existing manually-created returns

incorrectly handle partial returns

incorrectly handle multiple edits of the same sale

Our process
We'll do it like this:

Part 1 → understand the current Sale Edit flow.
You give me the relevant code/data.

Part 2 → understand current returns / return_items creation.

Part 3 → design the exact return calculation.

Part 4 → implement only the return-detection portion.

Part 5 → test it with SQL/code checks.

Part 6 → connect it to stock/costing only after the return records are correct.

Part 7 → test repeated edits, quantity reduction, complete removal, and mixed changes.

the system should compare the old sale items with the new edited sale items.

For each product:

Old quantity = 10, new quantity = 7 → 3 units returned

Old quantity = 10, new quantity = 10 → no return

Old quantity = 10, new quantity = 12 → 2 additional units sold, no return

Old item quantity = 5, item completely removed → 5 units returned

Product remains but quantity decreases → only the difference becomes a return.

The return must be recorded in sma_return_items and its corresponding return/header data.

The returned quantity must go back into inventory correctly.

The return should be visible in your existing Return Report, including the returned item and amount.

We should not create duplicate returns if the same sale is edited multiple times.

Existing purchase/costing/stock logic must remain intact.