import xml.etree.ElementTree as ET
import re


def extract_data_from_xml_to_dict(file_path):

    tree = ET.parse(file_path)
    root = tree.getroot()

    mix_data = {}

    for mix in root.iter('Mix'):
        mix_name = mix.find('Mix_name').text if mix.find('Mix_name') is not None else None
        mix_ml = mix.find('Mix_ml').text if mix.find('Mix_ml') is not None else None
        
        # Assurer que mix_name et mix_ml ne sont pas None ou vides avant de les ajouter
        if mix_name and mix_ml and mix_ml.strip():
            numbers_in_mix_name = ''.join(extract_numbers(mix_name))
            mix_data[numbers_in_mix_name] = mix_ml

    return mix_data

def extract_numbers(s):
    return re.findall(r'\d+', s)

file_path = 'C:/Users/elsaa/Downloads/test.XML' 
mix_data = extract_data_from_xml_to_dict(file_path)

print("Données extraites :", mix_data)