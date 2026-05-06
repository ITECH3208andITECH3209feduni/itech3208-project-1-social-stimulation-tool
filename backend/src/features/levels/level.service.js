import { LevelModel } from "#models/index.js";
import LevelMessages from "./level.message.js";

const LevelService = {
    insertLevel: async (name) => {
        const exist = await LevelModel.findOne({ name });

        if (exist) {
            throw LevelMessages.error.TUTORIAL_LEVEL_IS_EXIST();
        }

        const level = await LevelModel.create({ name });
        return level;
    },

    bulkInsertLevels: async () => {
        const count = await LevelModel.countDocuments();
        const isEmpty = count == 0;

        if (!isEmpty) {
            throw LevelMessages.error.LIST_TUTORIAL_LEVELS_EXIST();
        }

        const levels = [{ name: "Beginner" }, { name: "Intermediate" }, { name: "Advanced" }];

        return await LevelModel.insertMany(levels);
    },

    getLevels: async () => {
        const levels = await LevelModel.find().lean();

        if (!levels || levels.length == 0) {
            throw LevelMessages.error.LIST_TUTORIAL_LEVELS_IS_EMPTY();
        }

        const cleanedLevels = levels.map((c) => {
            return {
                id: c._id,
                name: c.name,
            };
        });

        return {
            total: levels.length,
            levels: cleanedLevels,
        };
    },
};

export default LevelService;
