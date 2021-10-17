/* CREATE TABLE */
CREATE TABLE tacobellfoodinfo (
Food VARCHAR(100),
Serving_Size INTEGER,
Calories INTEGER,
Fat INTEGER,
Sugar INTEGER,
Carbs INTEGER,
Protein INTEGER,
Fiber INTEGER
);

INSERT INTO tacobellfoodinfo(Food, Serving_Size, Calories, Fat, Protein, Carbs, Fiber, Sugar)
VALUES
(
'Taco Bell Taco with Beef', 100, 229, 12, 8, 19, 3, 0
);

INSERT INTO tacobellfoodinfo(Food, Serving_Size, Calories, Fat, Protein, Carbs, Fiber, Sugar)
VALUES
(
'Taco Bell Soft Taco with Beef', 100, 206, 9, 9, 20, 2, 1
);

INSERT INTO tacobellfoodinfo(Food, Serving_Size, Calories, Fat, Protein, Carbs, Fiber, Sugar)
VALUES
(
'Taco Bell Soft Taco with Chicken', 100, 189, 6, 13, 19, 1, 1
);

INSERT INTO tacobellfoodinfo(Food, Serving_Size, Calories, Fat, Protein, Carbs, Fiber, Sugar)
VALUES
(
'Taco Bell Soft Taco with Steak', 100, 225, 12, 11, 17, 1, 0
);

INSERT INTO tacobellfoodinfo(Food, Serving_Size, Calories, Fat, Protein, Carbs, Fiber, Sugar)
VALUES
(
'Taco Bell Bean Burrito', 100, 209, 6, 7, 31, 4, 1
);

INSERT INTO tacobellfoodinfo(Food, Serving_Size, Calories, Fat, Protein, Carbs, Fiber, Sugar)
VALUES
(
'Taco Bell Burrito Supreme with Beef', 100, 183, 6, 7, 23, 3, 2
);

INSERT INTO tacobellfoodinfo(Food, Serving_Size, Calories, Fat, Protein, Carbs, Fiber, Sugar)
VALUES
(
'Taco Bell Burrito Supreme with Chicken', 100, 179, 6, 9, 20, 2, 0
);

INSERT INTO tacobellfoodinfo(Food, Serving_Size, Calories, Fat, Protein, Carbs, Fiber, Sugar)
VALUES
(
'Taco Bell Burrito Supreme with Steak', 100, 183, 7, 9, 20, 2, 0
);

INSERT INTO tacobellfoodinfo(Food, Serving_Size, Calories, Fat, Protein, Carbs, Fiber, Sugar)
VALUES
(
'Taco Bell Nachos', 100, 350, 21, 4, 34, 3, 2
);

INSERT INTO tacobellfoodinfo(Food, Serving_Size, Calories, Fat, Protein, Carbs, Fiber, Sugar)
VALUES
(
'Taco Bell Nachos Supreme', 100, 223, 12, 6, 21, 3, 1
);

SELECT * FROM tacobellfoodinfo;