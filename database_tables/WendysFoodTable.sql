/* CREATE TABLE */
CREATE TABLE wendysfoodinfo (
Food VARCHAR(100),
Serving_Size INTEGER,
Calories INTEGER,
Fat INTEGER,
Sugar INTEGER,
Carbs INTEGER,
Protein INTEGER,
Fiber INTEGER
);

INSERT INTO wendysfoodinfo(Food, Serving_Size, Calories, Fat, Protein, Carbs, Fiber, Sugar)
VALUES
(
'Wendys Single Hamburger', 100, 213, 10, 12, 16, 1, 0
);

INSERT INTO wendysfoodinfo(Food, Serving_Size, Calories, Fat, Protein, Carbs, Fiber, Sugar)
VALUES
(
'Wendys Single Hamburger with Cheese', 100, 221, 11, 14, 14, 1, 0
);

INSERT INTO wendysfoodinfo(Food, Serving_Size, Calories, Fat, Protein, Carbs, Fiber, Sugar)
VALUES
(
'Wendys Junior Hamburger', 100, 243, 8, 12, 28, 1, 0
);

INSERT INTO wendysfoodinfo(Food, Serving_Size, Calories, Fat, Protein, Carbs, Fiber, Sugar)
VALUES
(
'Wendys Junior Hamburger with Cheese', 100, 256, 11, 13, 24, 1, 0
);

INSERT INTO wendysfoodinfo(Food, Serving_Size, Calories, Fat, Protein, Carbs, Fiber, Sugar)
VALUES
(
'Wendys Double Cheeseburger', 100, 241, 14, 16, 11, 1, 0
);

INSERT INTO wendysfoodinfo(Food, Serving_Size, Calories, Fat, Protein, Carbs, Fiber, Sugar)
VALUES
(
'Wendys Homestyle Chicken Sandwich', 100, 214, 8, 13, 21, 1, 0
);

INSERT INTO wendysfoodinfo(Food, Serving_Size, Calories, Fat, Protein, Carbs, Fiber, Sugar)
VALUES
(
'Wendys Ultimate Grilled Chicken Sandwich', 100, 179, 5, 14, 18, 1, 0
);

INSERT INTO wendysfoodinfo(Food, Serving_Size, Calories, Fat, Protein, Carbs, Fiber, Sugar)
VALUES
(
'Wendys Chicken Nuggets', 100, 326, 22, 16, 14, 0, 0
);

INSERT INTO wendysfoodinfo(Food, Serving_Size, Calories, Fat, Protein, Carbs, Fiber, Sugar)
VALUES
(
'Wendys French Fries', 100, 301, 14, 3, 39, 4, 0
);

SELECT * FROM wendysfoodinfo;