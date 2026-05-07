import express from "express";
import { bodyMw, authMw } from "#middlewares/index.js";
import UserController from "./user.controller.js";

const router = express.Router();

router.get(
    "/get-user-infor",
    authMw.authorizeRole([authMw.UserRole.user]),
    UserController.getUserInfor,
);

export default router;
