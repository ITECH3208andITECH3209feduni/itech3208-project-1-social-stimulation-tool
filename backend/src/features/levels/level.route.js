import express from "express";
import LevelController from "./level.controller.js";
import { bodyMw } from "#middlewares/index.js";
import LevelSchema from "./level.validation.js";

const router = express.Router();

router.get("/", LevelController.getLevels);
router.post("/insert-level", bodyMw.validate(LevelSchema.insert), LevelController.insertLevel);
router.post("/bulk-insert-levels", LevelController.bulkInsertLevels);

export default router;
