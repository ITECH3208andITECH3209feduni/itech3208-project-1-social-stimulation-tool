import { v1 as uuidv1, v4 as uuidv4 } from "uuid";

const uuidUtil = {
    genV1: () => uuidv1(),
    genV4: () => uuidv4(),
};

export default uuidUtil;
