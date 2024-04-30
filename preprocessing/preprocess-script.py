import zipfile
import csv
import sys
import codecs
from tempfile import NamedTemporaryFile, mkdtemp
import os
import shutil

def fix_associatedMedia(zip_file_path):
    tempfile = NamedTemporaryFile(mode='w', delete=False, newline='')
    temp_dir = mkdtemp()
    # Extract multimedia links from the multimedia file in the ZIP archive
    url_list = {} # key: id, value: image url
    with zipfile.ZipFile(zip_file_path, 'r') as zip_ref:
        with zip_ref.open("multimedia.txt", 'r') as multimedia_csv:
            csv_reader = csv.DictReader(codecs.iterdecode(multimedia_csv, 'utf-8'), delimiter="\t")
            for row in csv_reader:
                url_list[row['id']] = row['identifier']
    # Write the links into a temporary occurrence file
    with zipfile.ZipFile(zip_file_path, 'r') as zip_ref:
        with zip_ref.open("occurrence.txt", 'r') as occurrence_csv:
            csv_reader = csv.DictReader(codecs.iterdecode(occurrence_csv, 'utf-8'), delimiter="\t")
            fields = csv_reader.fieldnames
            csv_writer = csv.DictWriter(tempfile, delimiter="\t", fieldnames=fields)
            csv_writer.writeheader()
            for row in csv_reader:
                if row['associatedMedia'] == "[see Simple Media extension]":
                    if row['id'] in url_list:
                        row['associatedMedia'] = url_list[row['id']]
                    csv_writer.writerow(row)
                else:
                    csv_writer.writerow(row)
            tempfile.close()
    # create a copy of the zip archive without the original occurrence.txt
    with zipfile.ZipFile(zip_file_path, 'r') as zip_ref:
        zip_ref.extractall(temp_dir)
        os.remove(os.path.join(temp_dir,"occurrence.txt"))
    # rezip with new occurrence.txt
    basename, _ = os.path.splitext(zip_file_path)
    new_path = basename + "-preprocessed"
    shutil.make_archive(new_path, 'zip', temp_dir)
    new_zip_path = new_path + ".zip"
    with zipfile.ZipFile(new_zip_path, 'a') as zip_ref:
        zip_ref.write(tempfile.name, "occurrence.txt")
    print("Success: " + new_zip_path)
    # change guid, add issue regarding update occurrence for images upon ingestion

if __name__ == "__main__":
    if len(sys.argv) != 2:
        print("Usage: python [preprocess-script.py] [path to dwca zip file]")
        sys.exit(1)
    
    zip_file_path = sys.argv[1]
    fix_associatedMedia(zip_file_path)