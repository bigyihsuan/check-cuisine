/* CREATE TABLE */
CREATE TABLE burgerkingfoodinfo (
Food VARCHAR(100),
Serving_Size INTEGER,
Calories INTEGER,
Fat INTEGER,
Sugar INTEGER,
Carbs INTEGER,
Protein INTEGER,
Fiber INTEGER
);

INSERT INTO burgerkingfoodinfo(Food, Serving_Size, Calories, Fat, Protein, Carbs, Fiber, Sugar)
VALUES
(
'Burger King French Fries', 100, 280, 12, 3, 38, 2, 0
), (
'Burger King Hamburger', 100, 261, 10, 14, 26, 1, 5
), (
'Burger King Cheeseburger', 100, 286, 14, 14, 23, 1, 4
), (
'Burger King Whopper', 100, 233, 12, 10, 18, 1, 4
), (
'Burger King Whopper with Cheese', 100, 250, 15, 11, 16, 1, 4
), (
'Burger King Double Whopper', 100, 252, 15, 13, 13, 1, 3
), (
'Burger King Double Whopper with Cheese', 100, 266, 17, 14, 13, 1, 3
), (
'Burger King Chicken Strips', 100, 292, 15, 18, 20, 1, 0
), (
'Burger King Premium Fish Sandwich', 100, 260, 12, 10, 26, 0, 3
), (
'Burger King Vanilla Shake', 100, 168, 8, 3, 19, 0, 11
);

SELECT * FROM burgerkingfoodinfo;
