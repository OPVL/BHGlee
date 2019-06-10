/**
 * Enumeration of different field types associated with Bullhorn Data.
 */
export declare enum FieldType {
    ID = 0,
    COMPOSITE = 1,
    TO_ONE = 2,
    TO_MANY = 3,
    SCALAR = 4,
}
export interface FieldMapOption {
    value?: any;
    label?: string;
    readOnly?: boolean;
}
export interface FieldMap {
    name: string;
    type?: string;
    dataType?: FieldType;
    dataSpecialization?: string;
    maxLength?: number;
    confidential?: string;
    optional?: boolean;
    label?: string;
    required?: boolean;
    readOnly?: boolean;
    multiValue?: boolean;
    inputType?: string;
    optionsType?: string;
    optionsUrl?: string;
    options?: FieldMapOption[];
    hideFromSearch?: boolean;
    sortOrder?: number;
    hint?: string;
    description?: string;
    associatedEntity?: any;
    value?: any;
    interactions?: any[];
    defaultValue?: any;
}
export interface FieldLayout {
    name: string;
    label: string;
    enabled: boolean;
    fields: string[];
}
