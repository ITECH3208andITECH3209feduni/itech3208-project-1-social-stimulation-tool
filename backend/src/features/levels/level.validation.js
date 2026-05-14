import Joi from "joi";
import LevelMessages from "./level.message.js";

const insertLevel = Joi.object({
    name: Joi.string().required().min(3).max(50).messages(LevelMessages.validation),
});

const LevelSchema = {
    insert: insertLevel
}

export default LevelSchema;
