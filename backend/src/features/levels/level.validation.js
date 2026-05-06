import Joi from "joi";
import LevelMessages from "./level.message.js";

const levelSchema = Joi.object({
    name: Joi.string().required().min(3).max(50).messages(LevelMessages.validation),
});

export default levelSchema;
